import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getMyFaults } from '../services/api';

export default function FaultsListScreen() {
  const navigation = useNavigation();
  const [faults, setFaults] = useState([]);
  const [loading, setLoading] = useState(false);

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

  useEffect(() => { load(); }, []);

  const renderItem = ({ item }) => {
    const title = item.customer || item.link || item.address || `Fault #${item.id}`;
    const status = item.status || 'Unknown status';
    const location = item.city && item.suburb ? `${item.city}, ${item.suburb}` : (item.city || item.suburb || '');
    return (
      <TouchableOpacity style={styles.card} onPress={() => navigation.navigate('FaultDetail', { id: item.id })}>
        <Text style={styles.cardTitle}>{title}</Text>
        <Text style={styles.cardSub}>{status}</Text>
        <Text style={styles.cardMeta}>{location}</Text>
      </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <Text style={styles.header}>My Faults</Text>
      <FlatList
        data={faults}
        keyExtractor={(i) => String(i.id)}
        renderItem={renderItem}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={load} />}
        contentContainerStyle={faults.length === 0 && { paddingTop: 64 }}
        ListEmptyComponent={<Text style={styles.empty}>No faults assigned.</Text>}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff', paddingHorizontal: 16 },
  header: { fontSize: 22, fontWeight: '700', color: '#0A66CC', textAlign: 'center', marginVertical: 16 },
  card: { borderWidth: 1, borderColor: '#E5E7EB', borderRadius: 10, padding: 12, marginBottom: 10 },
  cardTitle: { fontSize: 16, fontWeight: '600' },
  cardSub: { fontSize: 14, color: '#6B7280', marginTop: 4 },
  cardMeta: { fontSize: 12, color: '#9CA3AF', marginTop: 4 },
  empty: { textAlign: 'center', color: '#6B7280' }
});