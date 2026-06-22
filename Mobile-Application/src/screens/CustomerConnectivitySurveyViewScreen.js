import React, { useEffect, useMemo, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, RefreshControl, ActivityIndicator, Image } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { Feather } from '@expo/vector-icons';
import { customerConnectivitySurveyPhotoUrl, getAuthToken, getCustomerConnectivitySurvey } from '../services/api';
import { usePermissions } from '../hooks/usePermissions';
import { theme } from '../styles/theme';

function badgeStyle(status) {
  if (status === 'submitted') return { backgroundColor: 'rgba(34, 197, 94, 0.12)', borderColor: 'rgba(34, 197, 94, 0.35)', color: theme.colors.success, label: 'Submitted' };
  return { backgroundColor: 'rgba(245, 158, 11, 0.12)', borderColor: 'rgba(245, 158, 11, 0.35)', color: theme.colors.warning, label: 'Draft' };
}

function SectionCard({ title, children }) {
  return (
    <View style={styles.sectionCard}>
      <Text style={styles.sectionTitle}>{title}</Text>
      <View style={{ marginTop: theme.spacing.sm }}>{children}</View>
    </View>
  );
}

function Row({ label, value }) {
  return (
    <View style={styles.row}>
      <Text style={styles.rowLabel}>{label}</Text>
      <Text style={styles.rowValue} numberOfLines={2}>{value || '-'}</Text>
    </View>
  );
}

export default function CustomerConnectivitySurveyViewScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const id = route?.params?.id;
  const { hasPermission } = usePermissions();
  const canList = hasPermission('surveys-list');
  const canEdit = hasPermission('survey-edit');
  const token = getAuthToken();

  const [loading, setLoading] = useState(false);
  const [survey, setSurvey] = useState(null);

  const load = async () => {
    if (!id) return;
    setLoading(true);
    try {
      const res = await getCustomerConnectivitySurvey(id);
      setSurvey(res?.data || null);
    } catch (e) {
      setSurvey(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    navigation.setOptions({ title: 'Connectivity Survey' });
    if (canList) load();
  }, [id, canList]);

  const payload = survey?.payload && typeof survey.payload === 'object' ? survey.payload : {};
  const meta = payload?.meta || {};
  const general = payload?.general || {};
  const service = payload?.serviceRequirements || {};
  const permissions = payload?.permissions || {};
  const outdoor = payload?.outdoor || {};
  const indoor = payload?.indoor || {};

  const photosByLabel = useMemo(() => {
    const list = Array.isArray(survey?.photos) ? survey.photos : [];
    const out = {};
    list.forEach((p) => {
      const key = String(p.label || 'other');
      if (!out[key]) out[key] = [];
      out[key].push(p);
    });
    return out;
  }, [survey?.photos]);

  const labelName = (key) => {
    const map = {
      building_entry: 'Building entry access point',
      cabinet_space: 'Equipment space / cabinet location',
      nearest_manhole_pole: 'Nearest manhole / pole / duct access',
      route_obstacles: 'Route obstacles',
      power_point: 'Power connection point',
      indoor_route: 'Indoor cable route',
      termination_point: 'Termination point mounting location',
      route_sketch: 'Route sketch with measurements',
    };
    return map[key] || String(key || 'Photos');
  };

  if (!canList) {
    return (
      <SafeAreaView style={styles.container} edges={['bottom']}>
        <View style={styles.center}>
          <Feather name="lock" size={22} color={theme.colors.secondaryText} />
          <Text style={styles.centerText}>You don't have permission to view customer connectivity surveys.</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (!survey && loading) {
    return (
      <SafeAreaView style={styles.container} edges={['bottom']}>
        <View style={styles.center}>
          <ActivityIndicator color={theme.colors.primary} />
          <Text style={styles.centerText}>Loading survey...</Text>
        </View>
      </SafeAreaView>
    );
  }

  const b = badgeStyle(survey?.status);
  const title = survey?.customer_name || general?.customerName || survey?.site_name || general?.siteName || 'Connectivity Survey';

  const photoLabels = Object.keys(photosByLabel || {});

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <View style={styles.header}>
        <View style={{ flex: 1 }}>
          <Text style={styles.siteName} numberOfLines={1}>{title}</Text>
          <Text style={styles.subTitle} numberOfLines={1}>
            Account/JC: {survey?.account_or_jc_number || general?.accountOrJcNumber || '-'} • {survey?.site_name || general?.siteName || '-'}
          </Text>
        </View>
        <View style={[styles.badge, { backgroundColor: b.backgroundColor, borderColor: b.borderColor }]}>
          <Text style={[styles.badgeText, { color: b.color }]}>{b.label}</Text>
        </View>
      </View>

      <View style={styles.actionsRow}>
        {canEdit ? (
          <TouchableOpacity style={styles.actionBtn} onPress={() => navigation.navigate('CustomerConnectivitySurveyWizard', { surveyId: id, mode: 'edit' })}>
            <Feather name="edit-2" size={16} color={theme.colors.primary} />
            <Text style={styles.actionBtnText}>Edit</Text>
          </TouchableOpacity>
        ) : null}
      </View>

      <ScrollView
        contentContainerStyle={styles.scroll}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={load} tintColor={theme.colors.primary} />}
      >
        <SectionCard title="General">
          <Row label="Date" value={meta?.date || survey?.survey_date} />
          <Row label="Performed By" value={survey?.survey_performed_by || meta?.surveyPerformedBy} />
          <Row label="Customer Name" value={survey?.customer_name || general?.customerName} />
          <Row label="Account/JC Number" value={survey?.account_or_jc_number || general?.accountOrJcNumber} />
          <Row label="Site Name / Location" value={survey?.site_name || general?.siteName} />
          <Row label="Physical Address" value={survey?.physical_address || general?.physicalAddress} />
          <Row label="Coordinates" value={survey?.coordinates || general?.coordinates} />
          <Row label="Latitude" value={general?.latitude != null ? String(general.latitude) : (survey?.latitude != null ? String(survey.latitude) : '')} />
          <Row label="Longitude" value={general?.longitude != null ? String(general.longitude) : (survey?.longitude != null ? String(survey.longitude) : '')} />
          <Row label="Customer Contact" value={general?.customerContactName} />
          <Row label="Phone" value={general?.customerContactPhone} />
          <Row label="Email" value={general?.customerContactEmail} />
        </SectionCard>

        <SectionCard title="Service Requirements">
          <Row label="Service Type" value={service?.serviceType} />
          <Row label="Bandwidth Down (Mbps)" value={service?.bandwidthDown} />
          <Row label="Bandwidth Up (Mbps)" value={service?.bandwidthUp} />
          <Row label="Purpose" value={service?.servicePurpose} />
          <Row label="Redundancy" value={service?.redundancyRequired} />
          <Row label="Handover Interface" value={service?.handoverInterface} />
          <Row label="Public IPs" value={service?.publicIpsRequired} />
          <Row label="Public IP Qty" value={service?.publicIpsQty} />
          <Row label="VLAN / Routing Notes" value={service?.vlanNotes} />
        </SectionCard>

        <SectionCard title="Site Access & Permissions">
          <Row label="Access Contact" value={permissions?.accessContact} />
          <Row label="Survey Done With" value={permissions?.surveyDoneWith} />
          <Row label="Working Hours / Restrictions" value={permissions?.workingHours} />
          <Row label="Permissions Required" value={permissions?.permissionsRequired} />
          <Row label="Notes" value={permissions?.notes} />
        </SectionCard>

        <SectionCard title="Outdoor Connectivity">
          <Row label="Nearest POP / Node" value={outdoor?.nearestPopNode} />
          <Row label="Feeder / Switch / OLT" value={outdoor?.feederSwitchOlt} />
          <Row label="Free Port Available" value={outdoor?.freePortAvailable} />
          <Row label="Port ID" value={outdoor?.portId} />
          <Row label="Estimated Distance" value={outdoor?.estimatedDistance} />
          <Row label="Route Type" value={outdoor?.routeType} />
          <Row label="Existing Infrastructure" value={outdoor?.existingInfrastructure} />
          <Row label="Obstructions / Risks" value={outdoor?.obstructionsRisks} />
          <Row label="Nearest Manhole / Pole Reference" value={outdoor?.nearestManholePoleReference} />
          <Row label="Manhole / JB Details" value={outdoor?.manholeJbDetails} />
          <Row label="Proposed Manholes / Poles" value={outdoor?.proposedRefs} />
        </SectionCard>

        <SectionCard title="Indoor Assessment">
          <Row label="Space for Terminal Equipment" value={indoor?.spaceForEquipment} />
          <Row label="Cabinet / Rack Available" value={indoor?.cabinetAvailable} />
          <Row label="Cabinet Size / U" value={indoor?.cabinetSize} />
          <Row label="New Cabinet Required" value={indoor?.newCabinetRequired} />
          <Row label="Power Available" value={indoor?.powerAvailable} />
          <Row label="Socket Type" value={indoor?.socketType} />
          <Row label="Distance to Socket (m)" value={indoor?.distanceToSocket} />
          <Row label="Back-up Power" value={indoor?.backupPower} />
          <Row label="Air-conditioning" value={indoor?.airConditioning} />
          <Row label="Earthing" value={indoor?.earthing} />
          <Row label="Internal Cabling Route" value={indoor?.internalCablingRoute} />
          <Row label="Notes" value={indoor?.notes} />
        </SectionCard>

        <SectionCard title="Photos">
          {photoLabels.length === 0 ? (
            <Text style={styles.emptyPhotos}>No photos attached.</Text>
          ) : (
            photoLabels.map((label) => {
              const list = photosByLabel[label] || [];
              return (
                <View key={label} style={{ marginBottom: theme.spacing.md }}>
                  <Text style={styles.photoGroupTitle}>{labelName(label)}</Text>
                  <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.photoStrip}>
                    {list.map((p) => {
                      const isImage = String(p?.mime_type || '').startsWith('image/');
                      if (!isImage) {
                        return (
                          <View key={String(p.id)} style={styles.fileThumbWrap}>
                            <Feather name="file" size={18} color={theme.colors.secondaryText} />
                            <Text style={styles.fileThumbText} numberOfLines={2}>{p?.original_name || 'Attachment'}</Text>
                          </View>
                        );
                      }
                      return (
                        <View key={String(p.id)} style={styles.photoThumbWrap}>
                          <Image
                            source={{ uri: customerConnectivitySurveyPhotoUrl(p.id), headers: token ? { Authorization: `Bearer ${token}` } : {} }}
                            style={styles.photoThumb}
                          />
                        </View>
                      );
                    })}
                  </ScrollView>
                </View>
              );
            })
          )}
        </SectionCard>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', gap: 10, padding: theme.spacing.lg },
  centerText: { color: theme.colors.secondaryText, textAlign: 'center' },
  header: {
    paddingHorizontal: theme.spacing.lg,
    paddingTop: theme.spacing.md,
    paddingBottom: theme.spacing.sm,
    flexDirection: 'row',
    gap: 10,
    alignItems: 'center',
  },
  siteName: { fontSize: theme.fontSizes.lg, fontWeight: '800', color: theme.colors.text },
  subTitle: { marginTop: 2, color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm },
  badge: {
    borderWidth: 1,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: theme.borderRadius.circle,
  },
  badgeText: { fontSize: theme.fontSizes.xs, fontWeight: '800' },
  actionsRow: { paddingHorizontal: theme.spacing.lg, paddingBottom: theme.spacing.sm, flexDirection: 'row', gap: 10 },
  actionBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: 'rgba(59,130,246,0.08)',
    borderWidth: 1,
    borderColor: 'rgba(59,130,246,0.25)',
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: theme.borderRadius.md,
  },
  actionBtnText: { color: theme.colors.primary, fontWeight: '800' },
  scroll: { padding: theme.spacing.lg, paddingBottom: theme.spacing.xxl },
  sectionCard: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    marginBottom: theme.spacing.md,
  },
  sectionTitle: { fontSize: theme.fontSizes.md, fontWeight: '800', color: theme.colors.text },
  row: { marginTop: 10, flexDirection: 'row', justifyContent: 'space-between', gap: 10 },
  rowLabel: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, flex: 1 },
  rowValue: { color: theme.colors.text, fontSize: theme.fontSizes.sm, fontWeight: '600', flex: 1, textAlign: 'right' },
  photoGroupTitle: { fontWeight: '800', color: theme.colors.text, marginBottom: 8 },
  photoStrip: { gap: 10, paddingRight: 6 },
  photoThumbWrap: { width: 88, height: 88, borderRadius: 10, overflow: 'hidden', borderWidth: 1, borderColor: theme.colors.border },
  photoThumb: { width: '100%', height: '100%' },
  fileThumbWrap: {
    width: 140,
    height: 88,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.surface,
    padding: 10,
    justifyContent: 'center',
    gap: 6,
  },
  fileThumbText: { fontSize: theme.fontSizes.xs, color: theme.colors.secondaryText, fontWeight: '700' },
  emptyPhotos: { color: theme.colors.secondaryText },
});
