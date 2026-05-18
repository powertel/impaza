import React, { useEffect, useMemo, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  RefreshControl,
  ActivityIndicator,
  Image,
  Modal,
  TextInput,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import * as ImagePicker from 'expo-image-picker';
import { Feather } from '@expo/vector-icons';
import { addLteSurveyRemark, getAuthToken, getLteSiteSurvey, lteSurveyPhotoUrl, lteSurveyRemarkFileUrl } from '../services/api';
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

export default function LteSiteSurveyViewScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const id = route?.params?.id;

  const [loading, setLoading] = useState(false);
  const [survey, setSurvey] = useState(null);
  const [remarkOpen, setRemarkOpen] = useState(false);
  const [remarkText, setRemarkText] = useState('');
  const [remarkFile, setRemarkFile] = useState(null);
  const [savingRemark, setSavingRemark] = useState(false);

  const token = getAuthToken();

  const load = async () => {
    if (!id) return;
    setLoading(true);
    try {
      const res = await getLteSiteSurvey(id);
      const data = res?.data || null;
      setSurvey(data);
    } catch (e) {
      setSurvey(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    navigation.setOptions({ title: 'LTE Survey' });
    load();
  }, [id]);

  const payload = survey?.payload && typeof survey.payload === 'object' ? survey.payload : {};
  const meta = payload?.meta || {};
  const general = payload?.general || {};
  const access = payload?.accessSecurity || {};
  const tower = payload?.tower || {};
  const tx = payload?.transmission || {};
  const power = payload?.power || {};
  const civil = payload?.civilWorks || {};
  const notes = payload?.notes || {};

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
      nearest_joint_box: 'Nearest Joint Box',
      fibre_route_towards_tower: 'Fibre Route Towards Tower',
      tower_overview: 'Tower Overview',
      new_plinth_space: 'New Plinth Space',
      power_connection_image: 'Power Connection',
      termination_point_image: 'Termination Point',
      route_sketch: 'Route Sketch',
    };
    return map[key] || String(key || 'Photos');
  };

  const pickRemarkImage = async () => {
    const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!perm.granted) {
      Alert.alert('Permission required', 'Allow media library access to attach an image.');
      return;
    }
    const result = await ImagePicker.launchImageLibraryAsync({ mediaTypes: ImagePicker.MediaTypeOptions.Images, quality: 0.8 });
    if (!result.canceled && result.assets?.[0]) {
      const a = result.assets[0];
      setRemarkFile({
        uri: a.uri,
        name: a.fileName || `remark-${Date.now()}.jpg`,
        type: a.mimeType || 'image/jpeg',
      });
    }
  };

  const saveRemark = async () => {
    if (!remarkText.trim()) {
      Alert.alert('Validation', 'Remark is required.');
      return;
    }
    setSavingRemark(true);
    try {
      const fd = new FormData();
      fd.append('remark', remarkText.trim());
      if (remarkFile?.uri) {
        fd.append('file', { uri: remarkFile.uri, name: remarkFile.name, type: remarkFile.type });
      }
      const res = await addLteSurveyRemark(id, fd);
      if (!res?.success) {
        Alert.alert('Error', res?.message || 'Failed to save remark.');
        return;
      }
      setRemarkText('');
      setRemarkFile(null);
      setRemarkOpen(false);
      await load();
    } catch (e) {
      Alert.alert('Error', 'Failed to save remark.');
    } finally {
      setSavingRemark(false);
    }
  };

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

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <View style={styles.header}>
        <View style={{ flex: 1 }}>
          <Text style={styles.siteName} numberOfLines={1}>{survey?.site_name || general?.siteName || 'Untitled Site'}</Text>
          <Text style={styles.subTitle} numberOfLines={1}>
            JC: {survey?.jc_number || general?.jcNumber || '-'} • {survey?.province_region || general?.provinceRegion || '-'}
          </Text>
        </View>
        <View style={[styles.badge, { backgroundColor: b.backgroundColor, borderColor: b.borderColor }]}>
          <Text style={[styles.badgeText, { color: b.color }]}>{b.label}</Text>
        </View>
      </View>

      <View style={styles.actionsRow}>
        <TouchableOpacity style={styles.actionBtn} onPress={() => navigation.navigate('LteSiteSurveyWizard', { surveyId: id, mode: 'edit' })}>
          <Feather name="edit-2" size={16} color={theme.colors.primary} />
          <Text style={styles.actionBtnText}>Edit</Text>
        </TouchableOpacity>
        <TouchableOpacity style={styles.actionBtn} onPress={() => setRemarkOpen(true)}>
          <Feather name="message-square" size={16} color={theme.colors.primary} />
          <Text style={styles.actionBtnText}>Remark</Text>
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scroll}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={load} tintColor={theme.colors.primary} />}
      >
        <SectionCard title="General">
          <Row label="Date" value={meta?.date || survey?.survey_date} />
          <Row label="Performed By" value={survey?.survey_performed_by || meta?.surveyPerformedBy} />
          <Row label="Coordinates" value={survey?.coordinates || general?.coordinates} />
          <Row label="Latitude" value={general?.latitude != null ? String(general.latitude) : (survey?.latitude != null ? String(survey.latitude) : '')} />
          <Row label="Longitude" value={general?.longitude != null ? String(general.longitude) : (survey?.longitude != null ? String(survey.longitude) : '')} />
          <Row label="Physical Address" value={survey?.physical_address || general?.physicalAddress} />
          <Row label="Contact Details" value={general?.contactDetails} />
        </SectionCard>

        <SectionCard title="Access & Tower">
          <Row label="Fence" value={access?.securityFenceAvailable ? 'Yes' : 'No'} />
          <Row label="Fence Condition" value={access?.conditionOfFence} />
          <Row label="24h Access" value={access?.siteAccess24h ? 'Yes' : 'No'} />
          <Row label="Guard" value={access?.guardAvailable ? 'Yes' : 'No'} />
          <Row label="Line of Sight" value={access?.lineOfSightAvailability ? 'Yes' : 'No'} />
          <Row label="Terrain" value={tower?.terrainType} />
          <Row label="Tower Owner" value={tower?.towerOwner} />
          <Row label="Height" value={tower?.allocatedHeight} />
        </SectionCard>

        <SectionCard title="Transmission">
          <Row label="Nearest Manhole" value={tx?.nearestManholeCoordinates} />
          <Row label="Existing Fibre Distance" value={tx?.distanceFromExistingFibre} />
          <Row label="POP" value={tx?.distanceFromNearestPop} />
          <Row label="Distance from POP" value={tx?.distanceFromNearestPop2} />
          <Row label="Allocated Port" value={tx?.allocatedPort} />
          <Row label="Required Backhaul" value={tx?.requiredBackhaulCapacity} />
          <Row label="Backhaul Type" value={tx?.backhaulType} />
        </SectionCard>

        <SectionCard title="Power">
          <Row label="Source" value={power?.powerSourceType} />
          <Row label="Phase" value={power?.phase} />
          <Row label="Voltage" value={power?.inputVoltage} />
          <Row label="Battery Capacity" value={power?.batteryCapacity} />
          <Row label="Battery Autonomy (hrs)" value={power?.batteryAutonomyHrs} />
          <Row label="Earthing" value={power?.earthingSystemInstalled} />
          <Row label="Cable Utility Source" value={power?.cableUtilitySourceToSite} />
          <Row label="DB Condition" value={power?.conditionOfDb} />
        </SectionCard>

        <SectionCard title="Civil Works">
          <Row label="Trenching Required" value={civil?.trenchingRequired ? 'Yes' : 'No'} />
          <Row label="Breaking Concrete/Tar" value={civil?.breakingConcreteTar ? 'Yes' : 'No'} />
          <Row label="Pole Planting" value={civil?.polePlantingRequired ? 'Yes' : 'No'} />
          <Row label="Plinth Construction" value={civil?.constructionOfPlinth ? 'Yes' : 'No'} />
          <Row label="New Manhole" value={civil?.newManholeRequired ? 'Yes' : 'No'} />
        </SectionCard>

        <SectionCard title="Notes">
          <Text style={styles.longText}>{notes?.notes || '-'}</Text>
        </SectionCard>

        <SectionCard title="Photos & Attachments">
          {Object.keys(photosByLabel).length === 0 ? (
            <Text style={styles.muted}>No attachments</Text>
          ) : (
            Object.entries(photosByLabel).map(([k, list]) => (
              <View key={k} style={{ marginBottom: theme.spacing.lg }}>
                <Text style={styles.photoGroupTitle}>{labelName(k)} ({list.length})</Text>
                <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.photoStrip}>
                  {list.map((p) => {
                    const isImage = String(p?.mime_type || '').startsWith('image/');
                    if (!isImage) {
                      return (
                        <View key={String(p.id)} style={styles.fileChip}>
                          <Feather name="file" size={16} color={theme.colors.secondaryText} />
                          <Text style={styles.fileChipText} numberOfLines={1}>{p?.original_name || 'Attachment'}</Text>
                        </View>
                      );
                    }
                    return (
                      <Image
                        key={String(p.id)}
                        source={{ uri: lteSurveyPhotoUrl(p.id), headers: token ? { Authorization: `Bearer ${token}` } : {} }}
                        style={styles.photoThumb}
                      />
                    );
                  })}
                </ScrollView>
              </View>
            ))
          )}
        </SectionCard>

        <SectionCard title="Remarks">
          {Array.isArray(survey?.remarks) && survey.remarks.length ? (
            survey.remarks.map((r) => {
              const isImage = String(r?.mime_type || '').startsWith('image/');
              return (
                <View key={String(r.id)} style={styles.remarkCard}>
                  <View style={styles.remarkHead}>
                    <Text style={styles.remarkUser}>{r?.user_name || 'User'}</Text>
                    <Text style={styles.remarkDate}>{r?.created_at ? String(r.created_at).slice(0, 16).replace('T', ' ') : ''}</Text>
                  </View>
                  <Text style={styles.remarkText}>{r?.remark || ''}</Text>
                  {r?.file_path ? (
                    isImage ? (
                      <Image
                        source={{ uri: lteSurveyRemarkFileUrl(r.id), headers: token ? { Authorization: `Bearer ${token}` } : {} }}
                        style={styles.remarkImage}
                      />
                    ) : (
                      <View style={styles.fileChip}>
                        <Feather name="paperclip" size={16} color={theme.colors.secondaryText} />
                        <Text style={styles.fileChipText} numberOfLines={1}>{r?.original_name || 'Attachment'}</Text>
                      </View>
                    )
                  ) : null}
                </View>
              );
            })
          ) : (
            <Text style={styles.muted}>No remarks yet</Text>
          )}
        </SectionCard>
      </ScrollView>

      <Modal visible={remarkOpen} transparent animationType="fade" onRequestClose={() => setRemarkOpen(false)}>
        <View style={styles.modalBackdrop}>
          <View style={styles.modalCard}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Add Remark</Text>
              <TouchableOpacity onPress={() => setRemarkOpen(false)}>
                <Feather name="x" size={20} color={theme.colors.secondaryText} />
              </TouchableOpacity>
            </View>
            <TextInput
              value={remarkText}
              onChangeText={setRemarkText}
              placeholder="Write a remark..."
              placeholderTextColor={theme.colors.muted}
              multiline
              style={styles.modalInput}
            />
            {remarkFile ? (
              <View style={styles.attachRow}>
                <Feather name="image" size={16} color={theme.colors.secondaryText} />
                <Text style={styles.attachName} numberOfLines={1}>{remarkFile.name}</Text>
                <TouchableOpacity onPress={() => setRemarkFile(null)}>
                  <Feather name="x" size={16} color={theme.colors.secondaryText} />
                </TouchableOpacity>
              </View>
            ) : null}
            <View style={styles.modalActions}>
              <TouchableOpacity style={styles.attachBtn} onPress={pickRemarkImage}>
                <Feather name="image" size={16} color={theme.colors.primary} />
                <Text style={styles.attachBtnText}>Attach Image</Text>
              </TouchableOpacity>
              <TouchableOpacity style={[styles.saveBtn, savingRemark && { opacity: 0.7 }]} onPress={saveRemark} disabled={savingRemark}>
                {savingRemark ? <ActivityIndicator color={theme.colors.white} /> : <Text style={styles.saveBtnText}>Save</Text>}
              </TouchableOpacity>
            </View>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', gap: 10 },
  centerText: { color: theme.colors.secondaryText },
  header: {
    paddingHorizontal: theme.spacing.lg,
    paddingTop: theme.spacing.md,
    paddingBottom: theme.spacing.sm,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  siteName: { fontSize: theme.fontSizes.lg, fontWeight: '800', color: theme.colors.text },
  subTitle: { marginTop: 2, fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText },
  badge: { borderWidth: 1, paddingHorizontal: 10, paddingVertical: 4, borderRadius: theme.borderRadius.circle },
  badgeText: { fontSize: theme.fontSizes.xs, fontWeight: '800' },
  actionsRow: { paddingHorizontal: theme.spacing.lg, flexDirection: 'row', gap: 10, paddingBottom: theme.spacing.sm },
  actionBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.surface,
  },
  actionBtnText: { fontWeight: '800', color: theme.colors.text, fontSize: theme.fontSizes.sm },
  scroll: { padding: theme.spacing.lg, paddingBottom: theme.spacing.xxl },
  sectionCard: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    padding: theme.spacing.md,
    marginBottom: theme.spacing.md,
  },
  sectionTitle: { fontSize: theme.fontSizes.md, fontWeight: '800', color: theme.colors.text },
  row: { flexDirection: 'row', justifyContent: 'space-between', gap: 12, paddingVertical: 8, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: theme.colors.border },
  rowLabel: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, flex: 1 },
  rowValue: { color: theme.colors.text, fontSize: theme.fontSizes.sm, fontWeight: '700', flex: 1, textAlign: 'right' },
  longText: { color: theme.colors.text, fontSize: theme.fontSizes.sm, lineHeight: 20 },
  muted: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm },
  photoGroupTitle: { fontSize: theme.fontSizes.sm, fontWeight: '800', color: theme.colors.text, marginBottom: 8 },
  photoStrip: { gap: 10, paddingBottom: 2 },
  photoThumb: { width: 120, height: 90, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.border },
  fileChip: {
    width: 180,
    height: 90,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.surface,
    padding: 10,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  fileChipText: { flex: 1, color: theme.colors.secondaryText, fontSize: theme.fontSizes.xs, fontWeight: '700' },
  remarkCard: { padding: theme.spacing.md, borderRadius: theme.borderRadius.md, borderWidth: 1, borderColor: theme.colors.border, backgroundColor: theme.colors.background, marginBottom: theme.spacing.sm },
  remarkHead: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 6 },
  remarkUser: { fontWeight: '800', color: theme.colors.text, fontSize: theme.fontSizes.sm },
  remarkDate: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.xs },
  remarkText: { color: theme.colors.text, fontSize: theme.fontSizes.sm, lineHeight: 20 },
  remarkImage: { marginTop: 10, width: '100%', height: 180, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.border },
  modalBackdrop: { flex: 1, backgroundColor: 'rgba(15,23,42,0.55)', alignItems: 'center', justifyContent: 'center', padding: theme.spacing.lg },
  modalCard: { width: '100%', backgroundColor: theme.colors.surface, borderRadius: theme.borderRadius.lg, borderWidth: 1, borderColor: theme.colors.border, padding: theme.spacing.md },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: theme.spacing.sm },
  modalTitle: { fontSize: theme.fontSizes.md, fontWeight: '800', color: theme.colors.text },
  modalInput: {
    minHeight: 100,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    padding: 12,
    color: theme.colors.text,
    textAlignVertical: 'top',
  },
  attachRow: { marginTop: 10, flexDirection: 'row', alignItems: 'center', gap: 8, padding: 10, borderRadius: theme.borderRadius.md, borderWidth: 1, borderColor: theme.colors.border, backgroundColor: theme.colors.background },
  attachName: { flex: 1, color: theme.colors.secondaryText, fontSize: theme.fontSizes.xs, fontWeight: '700' },
  modalActions: { marginTop: theme.spacing.md, flexDirection: 'row', justifyContent: 'space-between', gap: 10, alignItems: 'center' },
  attachBtn: { flexDirection: 'row', alignItems: 'center', gap: 8, paddingHorizontal: 12, paddingVertical: 10, borderRadius: theme.borderRadius.md, borderWidth: 1, borderColor: theme.colors.border },
  attachBtnText: { fontWeight: '800', color: theme.colors.text, fontSize: theme.fontSizes.sm },
  saveBtn: { flex: 1, backgroundColor: theme.colors.primary, paddingVertical: 12, borderRadius: theme.borderRadius.md, alignItems: 'center' },
  saveBtnText: { color: theme.colors.white, fontWeight: '800' },
});

