import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { getFault } from '../services/api';
import { theme } from '../styles/theme';

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
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} showsVerticalScrollIndicator={false}>
        <Text style={styles.title}>{fault.title || `Fault #${fault.id}`}</Text>
        <Text style={styles.row}>Status: {fault.status}</Text>
        <Text style={styles.row}>Location: {fault.location}</Text>
        <Text style={styles.row}>Description: {fault.description}</Text>

        <TouchableOpacity style={styles.primaryBtn} onPress={() => navigation.navigate('RectifyFault', { id })}>
          <Text style={styles.primaryBtnText}>Rectify Fault</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  center: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  container: { flex: 1, backgroundColor: theme.colors.white, padding: theme.spacing.lg },
  title: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.black, marginBottom: theme.spacing.md },
  row: { marginBottom: theme.spacing.sm, color: theme.colors.darkGray },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.xl },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' }
});