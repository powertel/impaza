import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, ActivityIndicator, Image, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute, useIsFocused } from '@react-navigation/native';
import { API_URL, getFault } from '../services/api';
import { usePermissions } from '../hooks/usePermissions';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function FaultDetailScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { id } = route.params || {};
  const [fault, setFault] = useState(null);
  const [remarks, setRemarks] = useState([]);
  const [loading, setLoading] = useState(false);
  const isFocused = useIsFocused();
  const [refreshing, setRefreshing] = useState(false);
  const { hasPermission, hasAnyPermission } = usePermissions();
  const ASSET_ORIGIN = (String(API_URL).replace(/\/$/, '')).replace(/\/api$/, '');

  const loadFault = async () => {
    setRefreshing(true);
    setLoading(true);
    try {
      const data = await getFault(id);
      setFault(data?.fault || data);
      setRemarks(data?.remarks || []);
    } catch (e) {
      // ignore errors for now
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    if (!isFocused) return;
    loadFault();
  }, [id, isFocused, route.params?.refetchAt]);


  if (loading || !fault) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={theme.colors.primary} />
      </View>
    );
  }

  const DetailRow = ({ icon, label, value }) => (
    <View style={styles.detailRow}>
      <View style={{ flexDirection: 'row', alignItems: 'center' }}>
        <View style={styles.iconContainer}>
          <Feather name={icon} size={16} color={theme.colors.secondaryText} />
        </View>
        <Text style={styles.detailLabel}>{label}</Text>
      </View>
      <Text style={styles.detailValue}>{value || 'N/A'}</Text>
    </View>
  );

  const StatusBadge = ({ status, priority }) => {
    let color = theme.colors.info;
    if (status === 'Resolved') color = theme.colors.success;
    if (status === 'Pending') color = theme.colors.warning;
    
    return (
      <View style={styles.badgeContainer}>
        <View style={[styles.badge, { backgroundColor: color + '20' }]}>
          <Text style={[styles.badgeText, { color }]}>{status}</Text>
        </View>
        {priority && (
          <View style={[styles.badge, { backgroundColor: theme.colors.danger + '20', marginLeft: 8 }]}>
            <Text style={[styles.badgeText, { color: theme.colors.danger }]}>{priority}</Text>
          </View>
        )}
      </View>
    );
  };

  const RemarkCard = ({ remark }) => (
    <View style={styles.remarkCard}>
      <View style={styles.remarkHeader}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{(remark.name || 'U').charAt(0)}</Text>
        </View>
        <View>
          <Text style={styles.remarkAuthor}>{remark.name || 'Unknown'}</Text>
          <Text style={styles.remarkDate}>{new Date(remark.created_at).toLocaleDateString()}</Text>
        </View>
      </View>
      <Text style={styles.remarkText}>{remark.remark}</Text>
      {remark.file_path ? (
        <View style={{ marginTop: 12 }}>
          <Image source={{ uri: `${ASSET_ORIGIN}/storage/${remark.file_path}` }} style={styles.remarkImage} resizeMode="contain" />
        </View>
      ) : null}
    </View>
  );

  return (
    <View style={styles.screen}>
      <SafeAreaView style={{ flex: 1 }} edges={["top","left","right"]}>
        <ScrollView contentContainerStyle={{ paddingBottom: theme.spacing.xxl }} showsVerticalScrollIndicator={false} refreshControl={<RefreshControl refreshing={refreshing} onRefresh={loadFault} tintColor={theme.colors.primary} />}>
          <View style={styles.header}>
            <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
              <Feather name="arrow-left" size={24} color={theme.colors.text} />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>Fault Details</Text>
            <View style={{ width: 40 }} />
          </View>

          <View style={styles.content}>
            <Text style={styles.title}>{fault.customer || `Fault #${fault.id}`}</Text>
            <StatusBadge status={fault.status} priority={fault.priorityLevel} />
            
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>Information</Text>
              <DetailRow icon="hash" label="Reference" value={fault.fault_ref_number} />
              <DetailRow icon="clock" label="Age" value={(function(){
                const started = fault.stage_started_at || fault.created_at;
                if (!started) return 'N/A';
                return new Date(started).toLocaleString();
              })()} />
              <DetailRow icon="activity" label="Service Type" value={fault.serviceType} />
              <DetailRow icon="user-check" label="Assigned To" value={fault.assignedToName || fault.assigned_to_name || fault.assignedTo} />
              <DetailRow icon="check-square" label="Assessed By" value={fault.assessedByName || fault.assessed_by_name || fault.assessedBy} />
            </View>

            <View style={styles.section}>
              <Text style={styles.sectionTitle}>Contact Person</Text>
              <DetailRow icon="user" label="Name" value={fault.contactName} />
              <DetailRow icon="phone" label="Phone" value={fault.phoneNumber} />
            </View>

            <View style={styles.section}>
              <Text style={styles.sectionTitle}>Location</Text>
              <DetailRow icon="map" label="City" value={fault.city} />
              <DetailRow icon="map-pin" label="Suburb" value={fault.suburb} />
              <DetailRow icon="navigation" label="Address" value={fault.address} />
            </View>

            {remarks && remarks.length > 0 && (
              <View style={styles.section}>
                <Text style={styles.sectionTitle}>Remarks History</Text>
                {remarks.map(remark => <RemarkCard key={remark.id} remark={remark} />)}
              </View>
            )}

            <View style={styles.actions}>
              {String(fault.status_id) == '3' && hasPermission('remark-create') && (
                <TouchableOpacity style={styles.secondaryBtn} onPress={() => navigation.navigate('AddRemark', { id })}>
                  <Feather name="message-square" size={18} color={theme.colors.text} style={{ marginRight: 8 }} />
                  <Text style={styles.secondaryBtnText}>Add Remark</Text>
                </TouchableOpacity>
              )}

              {String(fault.status_id) === '3' && hasAnyPermission(['re-assign-fault', 'assign-fault']) && (
                <TouchableOpacity style={[styles.secondaryBtn, { borderColor: theme.colors.danger }]} onPress={() => navigation.navigate('EscalateFault', { id })}>
                  <Feather name="alert-triangle" size={18} color={theme.colors.danger} style={{ marginRight: 8 }} />
                  <Text style={[styles.secondaryBtnText, { color: theme.colors.danger }]}>Escalate</Text>
                </TouchableOpacity>
              )}

              {String(fault.status_id) === '3' && hasAnyPermission(['rectify-fault', 'clear-fault', 'noc-clear-faults-clear', 'chief-tech-clear-faults-clear']) && (
                <TouchableOpacity style={styles.primaryBtn} onPress={() => navigation.navigate('RectifyFault', { id })}>
                  <Feather name="check-circle" size={18} color={theme.colors.white} style={{ marginRight: 8 }} />
                  <Text style={styles.primaryBtnText}>Rectify Fault</Text>
                </TouchableOpacity>
              )}
            </View>
          </View>
        </ScrollView>
      </SafeAreaView>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', backgroundColor: theme.colors.background },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: theme.spacing.lg, paddingVertical: theme.spacing.md },
  backBtn: { padding: 8, borderRadius: theme.borderRadius.circle, backgroundColor: theme.colors.surface },
  headerTitle: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.text },
  content: { padding: theme.spacing.lg },
  title: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text, marginBottom: theme.spacing.sm },
  badgeContainer: { flexDirection: 'row', marginBottom: theme.spacing.xl },
  badge: { paddingHorizontal: 12, paddingVertical: 6, borderRadius: theme.borderRadius.full },
  badgeText: { fontSize: theme.fontSizes.sm, fontWeight: '700' },
  section: { backgroundColor: theme.colors.card, borderRadius: theme.borderRadius.lg, padding: theme.spacing.lg, marginBottom: theme.spacing.lg, borderWidth: 1, borderColor: theme.colors.border },
  sectionTitle: { fontSize: theme.fontSizes.md, fontWeight: '700', color: theme.colors.text, marginBottom: theme.spacing.md },
  detailRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', paddingVertical: theme.spacing.sm, borderBottomWidth: 1, borderBottomColor: theme.colors.border },
  iconContainer: { width: 28, alignItems: 'center', justifyContent: 'center', marginRight: theme.spacing.sm },
  detailLabel: { fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText },
  detailValue: { fontSize: theme.fontSizes.md, color: theme.colors.text, fontWeight: '500', flex: 1, textAlign: 'right' },
  remarkCard: { backgroundColor: theme.colors.surface, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, marginBottom: theme.spacing.md, borderWidth: 1, borderColor: theme.colors.border },
  remarkHeader: { flexDirection: 'row', alignItems: 'center', marginBottom: theme.spacing.sm },
  avatar: { width: 32, height: 32, borderRadius: 16, backgroundColor: theme.colors.primary, alignItems: 'center', justifyContent: 'center', marginRight: theme.spacing.md },
  avatarText: { color: theme.colors.white, fontWeight: '700' },
  remarkAuthor: { fontSize: theme.fontSizes.sm, fontWeight: '600', color: theme.colors.text },
  remarkDate: { fontSize: theme.fontSizes.xs, color: theme.colors.secondaryText },
  remarkText: { fontSize: theme.fontSizes.md, color: theme.colors.text, lineHeight: 22 },
  remarkImage: { width: '100%', height: 200, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.background },
  actions: { gap: theme.spacing.md },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.borderRadius.md, paddingVertical: 14, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '700' },
  secondaryBtn: { backgroundColor: theme.colors.card, borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, paddingVertical: 14, flexDirection: 'row', alignItems: 'center', justifyContent: 'center' },
  secondaryBtnText: { color: theme.colors.text, fontSize: theme.fontSizes.md, fontWeight: '600' }
});
