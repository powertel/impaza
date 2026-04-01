import React, { useState, useEffect, useCallback } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, ActivityIndicator, TextInput } from 'react-native';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

const hexToRgba = (hex, alpha) => {
  if (typeof hex !== 'string' || !hex.startsWith('#') || (hex.length !== 7 && hex.length !== 4)) {
    return `rgba(255,255,255,${alpha})`;
  }
  const h = hex.length === 4
    ? `#${hex[1]}${hex[1]}${hex[2]}${hex[2]}${hex[3]}${hex[3]}`
    : hex;
  const r = parseInt(h.slice(1, 3), 16);
  const g = parseInt(h.slice(3, 5), 16);
  const b = parseInt(h.slice(5, 7), 16);
  return `rgba(${r},${g},${b},${alpha})`;
};

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
  const assignedTo = item.assignedToName;
  const assessedBy = item.assessedBy;

  const getStatusStyle = () => {
    const statusText = String(item.status || '').toLowerCase();
    const id = Number(item.status_id);

    if (statusText.includes('restor') || statusText.includes('resolv') || id === 6) {
      return { color: theme.colors.resolved, bg: hexToRgba(theme.colors.resolved, 0.12), border: hexToRgba(theme.colors.resolved, 0.28) };
    }
    if (statusText.includes('rectif') || id === 4) {
      return { color: theme.colors.warning, bg: hexToRgba(theme.colors.warning, 0.12), border: hexToRgba(theme.colors.warning, 0.28) };
    }
    if (statusText.includes('escalat') || id === 5) {
      return { color: theme.colors.escalated, bg: hexToRgba(theme.colors.escalated, 0.12), border: hexToRgba(theme.colors.escalated, 0.28) };
    }
    if (statusText.includes('refer') || statusText.includes('referr')) {
      return { color: theme.colors.referred, bg: hexToRgba(theme.colors.referred, 0.12), border: hexToRgba(theme.colors.referred, 0.28) };
    }
    if (statusText.includes('pending') || statusText.includes('await') || id === 1) {
      return { color: theme.colors.pending, bg: hexToRgba(theme.colors.pending, 0.12), border: hexToRgba(theme.colors.pending, 0.28) };
    }

    return { color: theme.colors.assigned, bg: hexToRgba(theme.colors.assigned, 0.12), border: hexToRgba(theme.colors.assigned, 0.28) };
  };

  const getPriorityStyle = (p) => {
    switch (p?.trim().toLowerCase()) {
      case 'critical':
      case 'high':
        return { 
          bar: { backgroundColor: theme.colors.danger }, 
          tag: { backgroundColor: 'rgba(239, 68, 68, 0.1)' }, 
          text: { color: theme.colors.danger } 
        };
      case 'medium':
        return { 
          bar: { backgroundColor: theme.colors.warning }, 
          tag: { backgroundColor: 'rgba(245, 158, 11, 0.1)' }, 
          text: { color: theme.colors.warning } 
        };
      case 'low':
        return { 
          bar: { backgroundColor: theme.colors.success }, 
          tag: { backgroundColor: 'rgba(34, 197, 94, 0.1)' }, 
          text: { color: theme.colors.success } 
        };
      default:
        return { 
          bar: { backgroundColor: theme.colors.info }, 
          tag: { backgroundColor: 'rgba(59, 130, 246, 0.1)' }, 
          text: { color: theme.colors.info } 
        };
    }
  };

  const priorityStyle = getPriorityStyle(priority);
  const statusStyle = getStatusStyle();

  return (
    <TouchableOpacity style={styles.card} onPress={() => onPress(item)}>
      <View style={styles.cardContent}>
        <View style={styles.cardHeader}>
          <Text style={styles.reference}>Fault #{reference.replace('ID: ', '')}</Text>
          <View style={[styles.priorityTag, priorityStyle.tag]}>
            <Text style={[styles.priorityTagText, priorityStyle.text]}>{priority.toUpperCase()}</Text>
          </View>
        </View>
        
        <Text style={styles.faultTitle}>{item.title || 'Internet connectivity issue'}</Text>
        
        <View style={styles.detailRow}>
          <Feather name="user" size={14} color={theme.colors.secondaryText} style={{ marginRight: 6 }} />
          <Text style={styles.detailText}>{customerName}</Text>
          <View style={{ width: 12 }} />
          <Feather name="map-pin" size={14} color={theme.colors.secondaryText} style={{ marginRight: 6 }} />
          <Text style={styles.detailText}>{item.city || 'Location N/A'}</Text>
        </View>

        <View style={styles.divider} />

        {(assignedTo || assessedBy) ? (
          <View style={styles.metaRow}>
            {assignedTo ? (
              <View style={styles.metaItem}>
                <Feather name="user-check" size={14} color={theme.colors.secondaryText} style={{ marginRight: 6 }} />
                <Text style={styles.metaText} numberOfLines={1}>Assigned: {assignedTo}</Text>
              </View>
            ) : null}
            {assessedBy ? (
              <View style={styles.metaItem}>
                <Feather name="check-square" size={14} color={theme.colors.secondaryText} style={{ marginRight: 6 }} />
                <Text style={styles.metaText} numberOfLines={1}>Assessed: {assessedBy}</Text>
              </View>
            ) : null}
          </View>
        ) : null}

        <View style={styles.cardFooter}>
          <View style={styles.footerLeft}>
            <Feather name="clock" size={14} color={theme.colors.secondaryText} style={{ marginRight: 6 }} />
            <Text style={styles.age}>{age}</Text>
          </View>
          
          <View style={[styles.statusBadge, { backgroundColor: statusStyle.bg, borderColor: statusStyle.border }]}>
            <Text style={[styles.statusText, { color: statusStyle.color }]}>
              {status}
            </Text>
          </View>
          
          <Feather name="chevron-right" size={18} color={theme.colors.secondaryText} />
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
  renderExtra,
  ListHeaderComponent,
  onDataLoaded
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
      
      if (onDataLoaded) {
        onDataLoaded(newItems);
      }
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
          <Feather name="chevron-left" size={20} color={page === 1 ? theme.colors.muted : theme.colors.text} />
          <Text style={[styles.pageBtnText, page === 1 && styles.disabledPageBtnText]}>Prev</Text>
        </TouchableOpacity>
        
        <Text style={styles.pageInfo}>Page {page} of {lastPage}</Text>
        
        <TouchableOpacity 
          style={[styles.pageBtn, page === lastPage && styles.disabledPageBtn]} 
          onPress={handleNextPage}
          disabled={page === lastPage || loading}
        >
          <Text style={[styles.pageBtnText, page === lastPage && styles.disabledPageBtnText]}>Next</Text>
          <Feather name="chevron-right" size={20} color={page === lastPage ? theme.colors.muted : theme.colors.text} />
        </TouchableOpacity>
      </View>
    );
  };

  return (
    <View style={styles.container}>
      {ListHeaderComponent}
      
      <View style={styles.searchContainer}>
        <Feather name="search" size={20} color={theme.colors.secondaryText} style={styles.searchIcon} />
        <TextInput
          style={styles.searchInput}
          placeholder="Search faults..."
          value={search}
          onChangeText={setSearch}
          placeholderTextColor={theme.colors.muted}
        />
        {search.length > 0 && (
          <TouchableOpacity onPress={() => setSearch('')}>
            <Feather name="x" size={18} color={theme.colors.secondaryText} />
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
  container: { flex: 1, backgroundColor: theme.colors.background },
  searchContainer: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    backgroundColor: theme.colors.surface, 
    marginHorizontal: theme.spacing.lg,
    marginVertical: theme.spacing.md,
    paddingHorizontal: theme.spacing.md, 
    borderRadius: theme.borderRadius.md, 
    borderWidth: 1, 
    borderColor: theme.colors.border, 
    height: 48 
  },
  searchIcon: { marginRight: theme.spacing.sm },
  searchInput: { flex: 1, fontSize: theme.fontSizes.md, color: theme.colors.text },
  listContent: { paddingHorizontal: theme.spacing.lg, paddingBottom: theme.spacing.xl },
  empty: { textAlign: 'center', color: theme.colors.secondaryText, marginTop: 64 },
  card: { 
    backgroundColor: theme.colors.card, 
    borderRadius: theme.borderRadius.lg, 
    marginBottom: theme.spacing.md, 
    overflow: 'hidden', 
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  cardContent: { padding: theme.spacing.md },
  cardHeader: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center', 
    marginBottom: theme.spacing.xs 
  },
  reference: { fontSize: theme.fontSizes.xs, color: theme.colors.secondaryText, fontWeight: '500' },
  priorityTag: { borderRadius: 6, paddingHorizontal: 8, paddingVertical: 2 },
  priorityTagText: { fontSize: 10, fontWeight: '700' },
  faultTitle: { 
    fontSize: theme.fontSizes.md, 
    fontWeight: '700', 
    color: theme.colors.text, 
    marginBottom: theme.spacing.sm 
  },
  detailRow: { flexDirection: 'row', alignItems: 'center', marginBottom: theme.spacing.md },
  detailText: { fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText },
  divider: { height: 1, backgroundColor: theme.colors.border, marginBottom: theme.spacing.sm },
  metaRow: { flexDirection: 'row', gap: theme.spacing.md, marginBottom: theme.spacing.sm },
  metaItem: { flexDirection: 'row', alignItems: 'center', flex: 1 },
  metaText: { fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText, flex: 1 },
  cardFooter: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  footerLeft: { flexDirection: 'row', alignItems: 'center' },
  age: { fontSize: theme.fontSizes.xs, color: theme.colors.secondaryText },
  statusBadge: { paddingHorizontal: 10, paddingVertical: 4, borderRadius: 8, borderWidth: 1 },
  statusText: { fontSize: 11, fontWeight: '700' },
  paginationContainer: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 10, marginBottom: 20, paddingHorizontal: 10 },
  pageBtn: { flexDirection: 'row', alignItems: 'center', padding: 8, backgroundColor: theme.colors.surface, borderRadius: 4, elevation: 1 },
  disabledPageBtn: { opacity: 0.5, backgroundColor: 'transparent', elevation: 0 },
  pageBtnText: { marginHorizontal: 4, fontWeight: '600', color: theme.colors.text },
  disabledPageBtnText: { color: theme.colors.muted },
  pageInfo: { fontSize: theme.fontSizes.md, fontWeight: '500', color: theme.colors.text },
});
