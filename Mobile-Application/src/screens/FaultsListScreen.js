import React, { useEffect, useState } from 'react';
import { View, Text, FlatList, TouchableOpacity, StyleSheet, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getMyFaults } from '../services/api';
import { theme } from '../styles/theme';

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
  container: { flex: 1, backgroundColor: theme.colors.white, paddingHorizontal: theme.spacing.lg },
  header: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.primary, textAlign: 'center', marginVertical: theme.spacing.lg },
  card: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.md, padding: theme.spacing.md, marginBottom: theme.spacing.md },
  cardTitle: { fontSize: theme.fontSizes.md, fontWeight: '600' },
  cardSub: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginTop: theme.spacing.xs },
  cardMeta: { fontSize: theme.fontSizes.xs, color: theme.colors.gray, marginTop: theme.spacing.xs },
  empty: { textAlign: 'center', color: theme.colors.gray }
});