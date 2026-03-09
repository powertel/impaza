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
    loadData(1, debouncedSearch);
  }, [debouncedSearch]);

  const loadData = async (pageNum, query) => {
    setLoading(true);
    try {
      const response = await fetchData({ page: pageNum, q: query });
      
      const newItems = Array.isArray(response) ? response : (response?.faults || []);
      const pagination = response?.pagination || {};
      
      setData(newItems);
      setLastPage(pagination.last_page || 1);
      setPage(pageNum); // Update current page state
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
    loadData(1, debouncedSearch);
  };

  const handleNextPage = () => {
    if (page < lastPage && !loading) {
      loadData(page + 1, debouncedSearch);
    }
  };

  const handlePrevPage = () => {
    if (page > 1 && !loading) {
      loadData(page - 1, debouncedSearch);
    }
  };

  const renderListItem = ({ item }) => {
    if (renderItem) return renderItem({ item });
    return <DefaultFaultCard item={item} onPress={onPressItem} renderExtra={renderExtra} />;
  };

  const renderFooter = () => {
    if (loading && data.length === 0) return null; // Initial load handled by ListEmptyComponent or overlay
    
    return (
      <View style={styles.paginationContainer}>
        <TouchableOpacity 
          style={[styles.pageBtn, page === 1 && styles.disabledPageBtn]} 
          onPress={handlePrevPage}
          disabled={page === 1 || loading}
        >
          <Feather name="chevron-left" size={20} color={page === 1 ? theme.colors.lightGray : theme.colors.dark} />
          <Text style={[styles.pageBtnText, page === 1 && styles.disabledPageBtnText]}>Prev</Text>
        </TouchableOpacity>
        
        <Text style={styles.pageInfo}>Page {page} of {lastPage}</Text>
        
        <TouchableOpacity 
          style={[styles.pageBtn, page === lastPage && styles.disabledPageBtn]} 
          onPress={handleNextPage}
          disabled={page === lastPage || loading}
        >
          <Text style={[styles.pageBtnText, page === lastPage && styles.disabledPageBtnText]}>Next</Text>
          <Feather name="chevron-right" size={20} color={page === lastPage ? theme.colors.lightGray : theme.colors.dark} />
        </TouchableOpacity>
      </View>
    );
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

      {loading && data.length === 0 ? (
        <ActivityIndicator style={{ marginTop: 40 }} size="large" color={theme.colors.primary} />
      ) : (
        <FlatList
          data={data}
          keyExtractor={(item) => String(item.id)}
          renderItem={renderListItem}
          onRefresh={handleRefresh}
          refreshing={refreshing}
          contentContainerStyle={styles.listContent}
          ListFooterComponent={renderFooter}
          ListEmptyComponent={<Text style={styles.empty}>{emptyMessage}</Text>}
        />
      )}
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
  paginationContainer: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 10, marginBottom: 20, paddingHorizontal: 10 },
  pageBtn: { flexDirection: 'row', alignItems: 'center', padding: 8, backgroundColor: theme.colors.white, borderRadius: 4, elevation: 1 },
  disabledPageBtn: { opacity: 0.5, backgroundColor: 'transparent', elevation: 0 },
  pageBtnText: { marginHorizontal: 4, fontWeight: '600', color: theme.colors.dark },
  disabledPageBtnText: { color: theme.colors.gray },
  pageInfo: { fontSize: theme.fontSizes.md, fontWeight: '500', color: theme.colors.dark },
});
