import React, { useEffect, useState, useMemo } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, RefreshControl, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useIsFocused } from '@react-navigation/native';
import { getMyFaults } from '../services/api';
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

const FaultCard = ({ item, onPress }) => {
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
    <TouchableOpacity style={styles.card} onPress={onPress}>
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
      </View>
    </TouchableOpacity>
  );
};

export default function FaultsListScreen() {
  const navigation = useNavigation();
  const isFocused = useIsFocused();
  const [faults, setFaults] = useState([]);
  const [loading, setLoading] = useState(false);
  const [activeFilter, setActiveFilter] = useState('All');

  const load = async () => {
    setLoading(true);
    try {
      const items = await getMyFaults();
      setFaults(Array.isArray(items) ? items : []);
    } catch (e) {
      // ignore for now
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { if (isFocused) load(); }, [isFocused]);

  const filters = ['All', 'Resolved', 'Not Yet Resolved'];

  const filteredFaults = useMemo(() => {
    if (activeFilter === 'All') return faults;
    if (activeFilter === 'Resolved') return faults.filter(f => String(f.status_id) === '4');
    if (activeFilter === 'Not Yet Resolved') return faults.filter(f => String(f.status_id) === '3');
    return faults;
  }, [faults, activeFilter]);

  const renderItem = ({ item }) => <FaultCard item={item} onPress={() => navigation.navigate('FaultDetail', { id: item.id })} />;

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>My Faults</Text>
        <TouchableOpacity onPress={() => {}}>
          <Feather name="plus-circle" size={28} color={theme.colors.primary} />
        </TouchableOpacity>
      </View>

      <View>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.pillsContainer}>
          {filters.map(filter => (
            <TouchableOpacity
              key={filter}
              style={[styles.pill, activeFilter === filter && styles.activePill]}
              onPress={() => setActiveFilter(filter)}
            >
              <Text style={[styles.pillText, activeFilter === filter && styles.activePillText]}>{filter}</Text>
            </TouchableOpacity>
          ))}
        </ScrollView>
      </View>

      <FlatList
        data={filteredFaults}
        keyExtractor={(i) => String(i.id)}
        renderItem={renderItem}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={load} />}
        contentContainerStyle={{ paddingTop: 16, paddingHorizontal: theme.spacing.lg }}
        ListEmptyComponent={<Text style={styles.empty}>No faults found.</Text>}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingHorizontal: theme.spacing.lg, paddingVertical: theme.spacing.md },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark },
  pillsContainer: { paddingHorizontal: theme.spacing.lg, paddingVertical: theme.spacing.sm },
  pill: { 
    backgroundColor: theme.colors.lightGray, 
    paddingHorizontal: theme.spacing.lg, 
    paddingVertical: theme.spacing.sm - 2, 
    borderRadius: 20, 
    marginRight: theme.spacing.md 
  },
  activePill: { 
    backgroundColor: theme.colors.primary 
  },
  pillText: { 
    color: theme.colors.dark, 
    fontWeight: '600' 
  },
  activePillText: { 
    color: theme.colors.white 
  },
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