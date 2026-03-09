import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator, TextInput } from 'react-native';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

const formatDistanceToNow = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const now = new Date();
  const seconds = Math.round(Math.abs(now - date) / 1000);
  const minutes = Math.round(seconds / 60);
  const hours = Math.round(minutes / 60);
  const days = Math.round(hours / 24);

  if (days > 0) return `${days}d ago`;
  if (hours > 0) return `${hours}h ago`;
  if (minutes > 0) return `${minutes}m ago`;
  return `${seconds}s ago`;
};

const DefaultFaultCard = ({ item, onPress, renderExtra }) => {
  const customerName = item.customer || 'N/A';
  const reference = item.fault_ref_number || `ID: ${item.id}`;
  const status = item.status || 'Unknown';
  const priority = item.priorityLevel || 'Normal';
  const age = formatDistanceToNow(item.stage_started_at || item.created_at);

  const getPriorityStyle = (p) => {
    switch (p?.trim().toLowerCase()) {
      case 'high':
        return { bar: styles.highPriorityBar, tag: styles.highPriorityTag, text: styles.highPriorityText };
      case 'medium':
        return { bar: styles.mediumPriorityBar, tag: styles.mediumPriorityTag, text: styles.mediumPriorityText };
      case 'low':
        return { bar: styles.lowPriorityBar, tag: styles.lowPriorityTag, text: styles.lowPriorityText };
      default:
        return { bar: styles.lowPriorityBar, tag: styles.lowPriorityTag, text: styles.lowPriorityText };
    }
  };

  const priorityStyle = getPriorityStyle(priority);

  return (
    <TouchableOpacity style={styles.card} onPress={() => onPress(item)}>
      <View style={[styles.priorityBar, priorityStyle.bar]} />
      <View style={styles.cardContent}>
        <View style={styles.cardHeader}>
          <Text style={styles.customerName}>{customerName}</Text>
          <View style={[styles.priorityTag, priorityStyle.tag]}>
            <Text style={[styles.priorityTagText, priorityStyle.text]}>{priority}</Text>
          </View>
        </View>
        <Text style={styles.reference}>Ref: {reference}</Text>
        <View style={styles.cardFooter}>
          <Text style={styles.status}>{status}</Text>
          <Text style={styles.age}>{age}</Text>
        </View>
        {renderExtra && renderExtra(item)}
      </View>
    </TouchableOpacity>
  );
};

export default function FaultList({ 
  fetchData, 
  renderItem, 
  onPressItem, 
  emptyMessage = "No faults found.",
  renderExtra
}) {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [search, setSearch] = useState('');
  const [debouncedSearch, setDebouncedSearch] = useState('');

  // Debounce search
  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedSearch(search);
    }, 500);
    return () => clearTimeout(handler);
  }, [search]);

  // Reset list when search changes
  useEffect(() => {
    setPage(1);
    setData([]);
    loadData(1, debouncedSearch, true);
  }, [debouncedSearch]);

  const loadData = async (pageNum, query, isRefresh = false) => {
    if (loading) return;
    setLoading(true);
    try {
      const response = await fetchData({ page: pageNum, q: query });
      
      const newItems = Array.isArray(response) ? response : (response?.faults || []);
      const pagination = response?.pagination || {};
      
      if (isRefresh || pageNum === 1) {
        setData(newItems);
      } else {
        setData(prev => [...prev, ...newItems]);
      }
      
      setLastPage(pagination.last_page || 1);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const handleRefresh = () => {
    setRefreshing(true);
    setPage(1);
    loadData(1, debouncedSearch, true);
  };

  const handleLoadMore = () => {
    if (!loading && page < lastPage) {
      const nextPage = page + 1;
      setPage(nextPage);
      loadData(nextPage, debouncedSearch);
    }
  };

  const renderListItem = ({ item }) => {
    if (renderItem) return renderItem({ item });
    return <DefaultFaultCard item={item} onPress={onPressItem} renderExtra={renderExtra} />;
  };

  return (
    <View style={styles.container}>
      <View style={styles.searchContainer}>
        <Feather name="search" size={20} color={theme.colors.gray} style={styles.searchIcon} />
        <TextInput
          style={styles.searchInput}
          placeholder="Search faults..."
          value={search}
          onChangeText={setSearch}
          placeholderTextColor={theme.colors.gray}
        />
        {search.length > 0 && (
          <TouchableOpacity onPress={() => setSearch('')}>
            <Feather name="x" size={18} color={theme.colors.gray} />
          </TouchableOpacity>
        )}
      </View>

      <FlatList
        data={data}
        keyExtractor={(item) => String(item.id)}
        renderItem={renderListItem}
        onRefresh={handleRefresh}
        refreshing={refreshing}
        onEndReached={handleLoadMore}
        onEndReachedThreshold={0.5}
        contentContainerStyle={styles.listContent}
        ListFooterComponent={loading && page > 1 ? <ActivityIndicator style={{ margin: 20 }} /> : null}
        ListEmptyComponent={!loading && <Text style={styles.empty}>{emptyMessage}</Text>}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1 },
  searchContainer: { flexDirection: 'row', alignItems: 'center', backgroundColor: theme.colors.white, margin: theme.spacing.lg, paddingHorizontal: theme.spacing.md, borderRadius: theme.spacing.sm, borderWidth: 1, borderColor: theme.colors.lightGray, height: 48 },
  searchIcon: { marginRight: theme.spacing.sm },
  searchInput: { flex: 1, fontSize: theme.fontSizes.md, color: theme.colors.dark },
  listContent: { paddingHorizontal: theme.spacing.lg, paddingBottom: theme.spacing.xl },
  empty: { textAlign: 'center', color: theme.colors.gray, marginTop: 64 },
  card: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.md, marginBottom: theme.spacing.md, flexDirection: 'row', overflow: 'hidden', elevation: 2, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.1, shadowRadius: 2 },
  priorityBar: { width: 6 },
  highPriorityBar: { backgroundColor: theme.colors.danger },
  mediumPriorityBar: { backgroundColor: theme.colors.warning },
  lowPriorityBar: { backgroundColor: theme.colors.success },
  cardContent: { flex: 1, padding: theme.spacing.md },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: theme.spacing.sm },
  customerName: { fontSize: theme.fontSizes.lg, fontWeight: '600', color: theme.colors.dark, flex: 1 },
  priorityTag: { borderRadius: 12, paddingHorizontal: theme.spacing.md, paddingVertical: 2, marginLeft: theme.spacing.sm },
  highPriorityTag: { backgroundColor: '#FEE2E2' },
  mediumPriorityTag: { backgroundColor: '#FEF3C7' },
  lowPriorityTag: { backgroundColor: '#D1FAE5' },
  priorityTagText: { fontSize: theme.fontSizes.xs, fontWeight: '700' },
  highPriorityText: { color: theme.colors.danger },
  mediumPriorityText: { color: theme.colors.warning },
  lowPriorityText: { color: theme.colors.success },
  reference: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.lg },
  cardFooter: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  status: { fontSize: theme.fontSizes.sm, color: theme.colors.dark, fontWeight: '500' },
  age: { fontSize: theme.fontSizes.xs, color: theme.colors.gray },
});
