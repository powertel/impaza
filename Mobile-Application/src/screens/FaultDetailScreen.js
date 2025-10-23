import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ActivityIndicator, ScrollView, Linking } from 'react-native';
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
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </SafeAreaView>
    );
  }

  const DetailRow = ({ label, value }) => (
    <View style={styles.row}>
      <Text style={styles.label}>{label}</Text>
      <Text style={styles.value}>{value || 'N/A'}</Text>
    </View>
  );

  const RemarkCard = ({ remark }) => (
    <View style={styles.remarkCard}>
      <Text style={styles.remarkText}>{remark.remark}</Text>
      <Text style={styles.remarkMeta}>By {remark.user?.name || 'Unknown'} on {new Date(remark.created_at).toLocaleDateString()}</Text>
    </View>
  );

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} showsVerticalScrollIndicator={false}>
        <Text style={styles.title}>{fault.customer?.name || `Fault #${fault.id}`}</Text>
        
        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Details</Text>
          <DetailRow label="Reference" value={fault.fault_ref_number} />
          <DetailRow label="Status" value={fault.status?.name} />
          <DetailRow label="Priority" value={fault.priorityLevel} />
          <DetailRow label="Reported" value={new Date(fault.created_at).toLocaleString()} />
          <DetailRow label="Service Type" value={fault.serviceType} />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Contact Person</Text>
          <DetailRow label="Name" value={fault.contactName} />
          <DetailRow label="Phone" value={fault.phoneNumber} />
          <DetailRow label="Email" value={fault.contactEmail} />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitle}>Location</Text>
          <DetailRow label="City" value={fault.city?.name} />
          <DetailRow label="Suburb" value={fault.suburb?.name} />
          <DetailRow label="Address" value={fault.address} />
        </View>

        {fault.remarks && fault.remarks.length > 0 && (
          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Remarks</Text>
            {fault.remarks.map(remark => <RemarkCard key={remark.id} remark={remark} />)}
          </View>
        )}

        <TouchableOpacity style={styles.primaryBtn} onPress={() => navigation.navigate('RectifyFault', { id })}>
          <Text style={styles.primaryBtnText}>Rectify Fault</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', backgroundColor: theme.colors.background },
  container: { flex: 1, backgroundColor: theme.colors.background, padding: theme.spacing.lg },
  title: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark, marginBottom: theme.spacing.lg },
  section: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.md, padding: theme.spacing.lg, marginBottom: theme.spacing.lg, elevation: 1, shadowColor: '#000', shadowOffset: { width: 0, height: 1 }, shadowOpacity: 0.05, shadowRadius: 2 },
  sectionTitle: { fontSize: theme.fontSizes.lg, fontWeight: '600', color: theme.colors.dark, marginBottom: theme.spacing.md },
  row: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: theme.spacing.sm, borderBottomWidth: 1, borderBottomColor: theme.colors.lightGray },
  label: { fontSize: theme.fontSizes.md, color: theme.colors.gray, fontWeight: '500' },
  value: { fontSize: theme.fontSizes.md, color: theme.colors.dark, fontWeight: '600', flex: 1, textAlign: 'right' },
  remarkCard: { backgroundColor: theme.colors.veryLightGray, borderRadius: theme.spacing.sm, padding: theme.spacing.md, marginBottom: theme.spacing.md },
  remarkText: { fontSize: theme.fontSizes.md, color: theme.colors.dark, marginBottom: theme.spacing.xs },
  remarkMeta: { fontSize: theme.fontSizes.sm, color: theme.colors.gray },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.lg },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' }
});