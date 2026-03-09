import React, { useEffect, useState, useMemo } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useIsFocused } from '@react-navigation/native';
import { getSectionFaults } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';
import { usePermissions } from '../hooks/usePermissions';

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

const FaultCard = ({ item, onPress, onReassign, canReassign }) => {
  const customerName = item.customer || 'N/A';
  const reference = item.fault_ref_number || `ID: ${item.id}`;
  const status = item.status || 'Unknown';
  const priority = item.priorityLevel || 'Normal';
  const age = formatDistanceToNow(item.stage_started_at || item.created_at);
  const assignedTo = item.assignedToName || 'Unassigned';

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
        <Text style={styles.assignedTo}>Assigned To: {assignedTo}</Text>
        <View style={styles.cardFooter}>
          <Text style={styles.status}>{status}</Text>
          <Text style={styles.age}>{age}</Text>
        </View>
        {canReassign && (
           <TouchableOpacity style={styles.reassignBtn} onPress={onReassign}>
             <Text style={styles.reassignBtnText}>Reassign</Text>
           </TouchableOpacity>
        )}
      </View>
    </TouchableOpacity>
  );
};

export default function SectionFaultsScreen() {
  const navigation = useNavigation();
  const isFocused = useIsFocused();
  const [faults, setFaults] = useState([]);
  const [loading, setLoading] = useState(false);
  const { hasPermission } = usePermissions();

  const load = async () => {
    setLoading(true);
    try {
      const items = await getSectionFaults();
      setFaults(Array.isArray(items) ? items : []);
    } catch (e) {
      // ignore for now
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { if (isFocused) load(); }, [isFocused]);

  const canReassign = hasPermission('re-assign-fault');

  const renderItem = ({ item }) => (
    <FaultCard 
      item={item} 
      onPress={() => navigation.navigate('FaultDetail', { id: item.id })}
      canReassign={canReassign}
      onReassign={() => navigation.navigate('AssignFault', { fault: item, mode: 'reassign' })}
    />
  );

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Section Faults</Text>
        <View style={{ flexDirection: 'row', alignItems: 'center' }}>
          <TouchableOpacity onPress={load} style={{ marginRight: theme.spacing.md }} accessibilityLabel="Refresh faults">
            <Feather name="refresh-ccw" size={22} color={theme.colors.dark} />
          </TouchableOpacity>
        </View>
      </View>

      <FlatList
        data={faults}
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
  reference: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: 4 },
  assignedTo: { fontSize: theme.fontSizes.sm, color: theme.colors.dark, marginBottom: theme.spacing.md, fontWeight: '500' },
  cardFooter: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  status: { fontSize: theme.fontSizes.sm, color: theme.colors.dark, fontWeight: '500' },
  age: { fontSize: theme.fontSizes.xs, color: theme.colors.gray },
  reassignBtn: { marginTop: 12, backgroundColor: theme.colors.lightGray, padding: 8, borderRadius: 4, alignItems: 'center' },
  reassignBtnText: { color: theme.colors.dark, fontWeight: '600', fontSize: 12 }
});
