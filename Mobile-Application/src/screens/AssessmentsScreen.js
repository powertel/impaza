import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, RefreshControl, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useIsFocused } from '@react-navigation/native';
import { getAssessments, assessFault } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

const FaultCard = ({ item, onPress, onAssess }) => {
  return (
    <TouchableOpacity style={styles.card} onPress={onPress}>
      <View style={styles.priorityBar} />
      <View style={styles.cardContent}>
        <View style={styles.cardHeader}>
          <Text style={styles.customerName}>{item.customer || 'N/A'}</Text>
          <Text style={styles.date}>{new Date(item.created_at).toLocaleDateString()}</Text>
        </View>
        <Text style={styles.reference}>Ref: {item.fault_ref_number}</Text>
        <Text style={styles.detail}>{item.serviceType || 'Unknown Type'}</Text>
        <TouchableOpacity style={styles.actionBtn} onPress={onAssess}>
          <Text style={styles.actionBtnText}>Assess</Text>
        </TouchableOpacity>
      </View>
    </TouchableOpacity>
  );
};

export default function AssessmentsScreen() {
  const navigation = useNavigation();
  const isFocused = useIsFocused();
  const [faults, setFaults] = useState([]);
  const [loading, setLoading] = useState(false);

  const load = async () => {
    setLoading(true);
    try {
      const items = await getAssessments();
      setFaults(Array.isArray(items) ? items : []);
    } catch (e) {
      // ignore
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { if (isFocused) load(); }, [isFocused]);

  const handleAssess = (fault) => {
    navigation.navigate('AssessFault', { fault });
  };

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Assessments</Text>
        <TouchableOpacity onPress={load}>
          <Feather name="refresh-ccw" size={22} color={theme.colors.dark} />
        </TouchableOpacity>
      </View>
      <FlatList
        data={faults}
        keyExtractor={(i) => String(i.id)}
        renderItem={({ item }) => <FaultCard item={item} onPress={() => navigation.navigate('FaultDetail', { id: item.id })} onAssess={() => handleAssess(item)} />}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={load} />}
        contentContainerStyle={{ padding: theme.spacing.lg }}
        ListEmptyComponent={<Text style={styles.empty}>No pending assessments.</Text>}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark },
  empty: { textAlign: 'center', color: theme.colors.gray, marginTop: 64 },
  card: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.md, marginBottom: theme.spacing.md, flexDirection: 'row', overflow: 'hidden', elevation: 2 },
  priorityBar: { width: 6, backgroundColor: theme.colors.info },
  cardContent: { flex: 1, padding: theme.spacing.md },
  cardHeader: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 4 },
  customerName: { fontSize: theme.fontSizes.md, fontWeight: '600', color: theme.colors.dark },
  date: { fontSize: theme.fontSizes.xs, color: theme.colors.gray },
  reference: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: 4 },
  detail: { fontSize: theme.fontSizes.sm, color: theme.colors.dark, marginBottom: 8 },
  actionBtn: { backgroundColor: theme.colors.primary, padding: 8, borderRadius: 4, alignItems: 'center', alignSelf: 'flex-start' },
  actionBtnText: { color: theme.colors.white, fontWeight: '600', fontSize: 12 },
});
