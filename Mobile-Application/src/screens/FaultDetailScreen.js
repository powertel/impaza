import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { getFault } from '../services/api';

export default function FaultDetailScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { id } = route.params || {};
  const [fault, setFault] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const load = async () => {
      setLoading(true);
      try {
        const data = await getFault(id);
        setFault(data);
      } catch (e) {
        // ignore errors for now
      } finally {
        setLoading(false);
      }
    };
    load();
  }, [id]);

  if (loading || !fault) {
    return (
      <SafeAreaView style={styles.center} edges={["top","left","right"]}> 
        <ActivityIndicator />
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <Text style={styles.title}>{fault.title || `Fault #${fault.id}`}</Text>
      <Text style={styles.row}>Status: {fault.status}</Text>
      <Text style={styles.row}>Location: {fault.location}</Text>
      <Text style={styles.row}>Description: {fault.description}</Text>

      <TouchableOpacity style={styles.primaryBtn} onPress={() => navigation.navigate('RectifyFault', { id })}>
        <Text style={styles.primaryBtnText}>Rectify Fault</Text>
      </TouchableOpacity>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  center: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  container: { flex: 1, backgroundColor: '#fff', padding: 16 },
  title: { fontSize: 18, fontWeight: '700', color: '#111827', marginBottom: 12 },
  row: { marginBottom: 8, color: '#374151' },
  primaryBtn: { backgroundColor: '#0A66CC', borderRadius: 8, paddingVertical: 14, alignItems: 'center', marginTop: 24 },
  primaryBtnText: { color: '#fff', fontSize: 16, fontWeight: '600' }
});