import React, { useEffect, useMemo, useRef, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  TextInput,
  RefreshControl,
  ActivityIndicator,
  Image,
  Alert,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as ImagePicker from 'expo-image-picker';
import { Feather } from '@expo/vector-icons';
import {
  createCustomerConnectivitySurvey,
  customerConnectivitySurveyPhotoUrl,
  deleteCustomerConnectivitySurveyPhoto,
  getAuthToken,
  getCustomerConnectivitySurvey,
  updateCustomerConnectivitySurvey,
} from '../services/api';
import { usePermissions } from '../hooks/usePermissions';
import { theme } from '../styles/theme';

const DRAFT_KEY = 'customer_connectivity_survey_draft_v1';

const todayString = () => {
  try {
    return new Date().toISOString().slice(0, 10);
  } catch (e) {
    return '';
  }
};

const emptyForm = {
  meta: {
    date: todayString(),
    surveyPerformedBy: '',
  },
  general: {
    customerName: '',
    accountOrJcNumber: '',
    siteName: '',
    physicalAddress: '',
    coordinates: '',
    latitude: '',
    longitude: '',
    customerContactName: '',
    customerContactPhone: '',
    customerContactEmail: '',
  },
  serviceRequirements: {
    serviceType: '',
    bandwidthDown: '',
    bandwidthUp: '',
    servicePurpose: '',
    redundancyRequired: '',
    handoverInterface: '',
    publicIpsRequired: '',
    publicIpsQty: '',
    vlanNotes: '',
  },
  permissions: {
    accessContact: '',
    surveyDoneWith: '',
    workingHours: '',
    permissionsRequired: '',
    notes: '',
  },
  outdoor: {
    nearestPopNode: '',
    feederSwitchOlt: '',
    freePortAvailable: '',
    portId: '',
    estimatedDistance: '',
    routeType: '',
    existingInfrastructure: '',
    obstructionsRisks: '',
    nearestManholePoleReference: '',
    manholeJbDetails: '',
    proposedRefs: '',
  },
  indoor: {
    spaceForEquipment: '',
    cabinetAvailable: '',
    cabinetSize: '',
    newCabinetRequired: '',
    powerAvailable: '',
    socketType: '',
    distanceToSocket: '',
    backupPower: '',
    airConditioning: '',
    earthing: '',
    internalCablingRoute: '',
    notes: '',
  },
  photos: {
    building_entry: [],
    cabinet_space: [],
    nearest_manhole_pole: [],
    route_obstacles: [],
    power_point: [],
    indoor_route: [],
    termination_point: [],
    route_sketch: [],
  },
};

function SectionTitle({ children }) {
  return <Text style={styles.sectionTitle}>{children}</Text>;
}

function Field({ label, value, onChangeText, placeholder, keyboardType }) {
  return (
    <View style={styles.field}>
      <Text style={styles.fieldLabel}>{label}</Text>
      <TextInput
        value={value}
        onChangeText={onChangeText}
        placeholder={placeholder}
        placeholderTextColor={theme.colors.muted}
        style={styles.input}
        keyboardType={keyboardType}
      />
    </View>
  );
}

export default function CustomerConnectivitySurveyWizardScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { hasPermission } = usePermissions();
  const canCreate = hasPermission('survey-create');
  const canEdit = hasPermission('survey-edit');

  const mode = route?.params?.mode || 'create';
  const surveyId = route?.params?.surveyId;
  const isEdit = mode === 'edit' && !!surveyId;
  const readOnly = isEdit ? !canEdit : !canCreate;

  const [step, setStep] = useState(0);
  const [loading, setLoading] = useState(false);
  const [loadingRemote, setLoadingRemote] = useState(false);
  const [form, setForm] = useState(emptyForm);
  const [remotePhotos, setRemotePhotos] = useState([]);
  const token = getAuthToken();

  const saveTimer = useRef(null);

  const steps = useMemo(() => ([
    { key: 'general', title: 'General' },
    { key: 'service', title: 'Service Requirements' },
    { key: 'permissions', title: 'Site Access & Permissions' },
    { key: 'outdoor', title: 'Outdoor Connectivity' },
    { key: 'indoor', title: 'Indoor Assessment' },
    { key: 'photos', title: 'Photos' },
    { key: 'review', title: 'Review' },
  ]), []);

  const totalSteps = steps.length;
  const currentTitle = steps[step]?.title || 'Connectivity Survey';

  useEffect(() => {
    navigation.setOptions({ title: 'Connectivity Survey' });
  }, []);

  const loadDraft = async () => {
    if (isEdit) return;
    try {
      const raw = await AsyncStorage.getItem(DRAFT_KEY);
      if (!raw) return;
      const parsed = JSON.parse(raw);
      if (!parsed || typeof parsed !== 'object') return;
      setForm((prev) => ({
        ...prev,
        meta: { ...prev.meta, ...(parsed.meta || {}) },
        general: { ...prev.general, ...(parsed.general || {}) },
        serviceRequirements: { ...prev.serviceRequirements, ...(parsed.serviceRequirements || {}) },
        permissions: { ...prev.permissions, ...(parsed.permissions || {}) },
        outdoor: { ...prev.outdoor, ...(parsed.outdoor || {}) },
        indoor: { ...prev.indoor, ...(parsed.indoor || {}) },
      }));
    } catch (e) {
    }
  };

  const saveDraftDebounced = (nextForm) => {
    if (isEdit) return;
    if (readOnly) return;
    if (saveTimer.current) clearTimeout(saveTimer.current);
    saveTimer.current = setTimeout(async () => {
      try {
        const payload = {
          meta: nextForm.meta,
          general: nextForm.general,
          serviceRequirements: nextForm.serviceRequirements,
          permissions: nextForm.permissions,
          outdoor: nextForm.outdoor,
          indoor: nextForm.indoor,
        };
        await AsyncStorage.setItem(DRAFT_KEY, JSON.stringify(payload));
      } catch (e) {
      }
    }, 400);
  };

  useEffect(() => {
    loadDraft();
    return () => {
      if (saveTimer.current) clearTimeout(saveTimer.current);
    };
  }, [mode]);

  useEffect(() => {
    if (!isEdit) return;
    let mounted = true;
    (async () => {
      setLoadingRemote(true);
      try {
        const res = await getCustomerConnectivitySurvey(surveyId);
        const data = res?.data || null;
        const payload = data?.payload && typeof data.payload === 'object' ? data.payload : {};
        if (!mounted) return;
        setForm({
          ...emptyForm,
          meta: { ...emptyForm.meta, ...(payload.meta || {}) },
          general: { ...emptyForm.general, ...(payload.general || {}) },
          serviceRequirements: { ...emptyForm.serviceRequirements, ...(payload.serviceRequirements || {}) },
          permissions: { ...emptyForm.permissions, ...(payload.permissions || {}) },
          outdoor: { ...emptyForm.outdoor, ...(payload.outdoor || {}) },
          indoor: { ...emptyForm.indoor, ...(payload.indoor || {}) },
          photos: emptyForm.photos,
        });
        setRemotePhotos(Array.isArray(data?.photos) ? data.photos : []);
      } catch (e) {
        if (mounted) {
          setRemotePhotos([]);
          setForm(emptyForm);
        }
      } finally {
        if (mounted) setLoadingRemote(false);
      }
    })();
    return () => { mounted = false; };
  }, [surveyId, mode]);

  const setBlock = (key, patch) => {
    setForm((prev) => {
      const next = { ...prev, [key]: { ...(prev[key] || {}), ...(patch || {}) } };
      saveDraftDebounced(next);
      return next;
    });
  };

  const addPhotos = (label, files) => {
    setForm((prev) => {
      const existing = Array.isArray(prev?.photos?.[label]) ? prev.photos[label] : (prev?.photos?.[label] ? [prev.photos[label]] : []);
      const nextList = [...existing, ...(Array.isArray(files) ? files : [files])].filter(Boolean);
      const next = { ...prev, photos: { ...(prev.photos || {}), [label]: nextList } };
      return next;
    });
  };

  const removeLocalPhoto = (label, index) => {
    setForm((prev) => {
      const list = Array.isArray(prev?.photos?.[label]) ? prev.photos[label] : [];
      const nextList = list.filter((_, i) => i !== index);
      return { ...prev, photos: { ...(prev.photos || {}), [label]: nextList } };
    });
  };

  const removeRemotePhoto = (photo) => {
    if (readOnly) return;
    const id = photo?.id;
    if (!id) return;
    Alert.alert('Remove', 'Remove this uploaded image/attachment?', [
      { text: 'Cancel', style: 'cancel' },
      {
        text: 'Remove',
        style: 'destructive',
        onPress: async () => {
          try {
            const res = await deleteCustomerConnectivitySurveyPhoto(id);
            if (!res?.success) {
              Alert.alert('Error', res?.message || 'Failed to remove image.');
              return;
            }
            setRemotePhotos((prev) => (Array.isArray(prev) ? prev.filter((p) => String(p?.id) !== String(id)) : []));
          } catch (e) {
            Alert.alert('Error', 'Failed to remove image.');
          }
        },
      },
    ]);
  };

  const requestLibraryPerm = async () => {
    const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!perm.granted) {
      Alert.alert('Permission required', 'Allow media library access to attach images.', [
        { text: 'Cancel', style: 'cancel' },
      ]);
      return false;
    }
    return true;
  };

  const requestCameraPerm = async () => {
    const perm = await ImagePicker.requestCameraPermissionsAsync();
    if (!perm.granted) {
      Alert.alert('Permission required', 'Allow camera access to capture images.', [
        { text: 'Cancel', style: 'cancel' },
      ]);
      return false;
    }
    return true;
  };

  const pickPhotoFor = async (label) => {
    try {
      const ok = await requestLibraryPerm();
      if (!ok) return;
      const result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        quality: 0.8,
        allowsMultipleSelection: true,
      });
      if (!result.canceled && Array.isArray(result.assets) && result.assets.length > 0) {
        const picked = result.assets.map((a) => ({
          uri: a.uri,
          name: a.fileName || `${label}-${Date.now()}-${Math.random().toString(16).slice(2)}.jpg`,
          type: a.mimeType || 'image/jpeg',
        }));
        addPhotos(label, picked);
      }
    } catch (e) {
      Alert.alert('Error', 'Failed to open gallery.');
    }
  };

  const capturePhotoFor = async (label) => {
    try {
      const ok = await requestCameraPerm();
      if (!ok) return;
      const result = await ImagePicker.launchCameraAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        quality: 0.8,
      });
      if (!result.canceled && result.assets?.[0]) {
        const a = result.assets[0];
        addPhotos(label, [{
          uri: a.uri,
          name: a.fileName || `${label}-${Date.now()}.jpg`,
          type: a.mimeType || 'image/jpeg',
        }]);
      }
    } catch (e) {
      Alert.alert('Error', 'Failed to capture photo.');
    }
  };

  const validateForSubmit = () => {
    const customerName = form?.general?.customerName?.trim();
    const siteName = form?.general?.siteName?.trim();
    if (!customerName) return 'Customer Name is required.';
    if (!siteName) return 'Site Name / Location is required.';
    return null;
  };

  const submit = async (status) => {
    if (readOnly) return;
    if (status === 'submitted') {
      const err = validateForSubmit();
      if (err) {
        Alert.alert('Validation', err);
        return;
      }
    }

    setLoading(true);
    try {
      const latRaw = String(form?.general?.latitude ?? '').trim();
      const lngRaw = String(form?.general?.longitude ?? '').trim();
      const lat = latRaw !== '' && !isNaN(Number(latRaw)) ? Number(latRaw) : null;
      const lng = lngRaw !== '' && !isNaN(Number(lngRaw)) ? Number(lngRaw) : null;

      const payload = {
        meta: form.meta,
        general: { ...(form.general || {}), latitude: lat, longitude: lng },
        serviceRequirements: form.serviceRequirements,
        permissions: form.permissions,
        outdoor: form.outdoor,
        indoor: form.indoor,
      };

      const fd = new FormData();
      fd.append('payload', JSON.stringify(payload));
      fd.append('status', status);

      Object.entries(form.photos || {}).forEach(([label, value]) => {
        const list = Array.isArray(value) ? value : (value ? [value] : []);
        list.forEach((file) => {
          if (!file?.uri) return;
          fd.append(`photos[${label}][]`, { uri: file.uri, name: file.name, type: file.type });
        });
      });

      const res = isEdit ? await updateCustomerConnectivitySurvey(surveyId, fd) : await createCustomerConnectivitySurvey(fd);
      if (!res?.success) {
        Alert.alert('Error', res?.message || 'Failed to save survey.');
        return;
      }

      if (status === 'submitted') {
        if (!isEdit) await AsyncStorage.removeItem(DRAFT_KEY);
        Alert.alert('Success', isEdit ? 'Survey updated and submitted.' : 'Survey submitted.', [
          {
            text: 'OK',
            onPress: () => {
              if (isEdit) {
                navigation.navigate('CustomerConnectivitySurveyView', { id: surveyId });
              } else {
                navigation.goBack();
              }
            },
          },
        ]);
      } else {
        Alert.alert('Saved', isEdit ? 'Draft updated.' : 'Draft saved.');
      }
    } catch (e) {
      Alert.alert('Error', 'Failed to save survey.');
    } finally {
      setLoading(false);
    }
  };

  const PhotoCard = ({ labelKey, title }) => {
    const list = Array.isArray(form?.photos?.[labelKey]) ? form.photos[labelKey] : (form?.photos?.[labelKey] ? [form.photos[labelKey]] : []);
    const remoteList = Array.isArray(remotePhotos) ? remotePhotos.filter((p) => String(p?.label || '') === String(labelKey)) : [];
    const hasAny = remoteList.length > 0 || list.length > 0;
    return (
      <View style={styles.photoCard}>
        <Text style={styles.photoTitle}>{title}</Text>
        {hasAny ? (
          <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.photoStrip}>
            {remoteList.map((p) => {
              const isImage = String(p?.mime_type || '').startsWith('image/');
              if (!isImage) {
                return (
                  <View key={`remote-${labelKey}-${p.id}`} style={styles.fileThumbWrap}>
                    <Feather name="file" size={18} color={theme.colors.secondaryText} />
                    <Text style={styles.fileThumbText} numberOfLines={2}>{p?.original_name || 'Attachment'}</Text>
                    {!readOnly ? (
                      <TouchableOpacity style={styles.photoRemove} onPress={() => removeRemotePhoto(p)}>
                        <Feather name="x" size={14} color={theme.colors.white} />
                      </TouchableOpacity>
                    ) : null}
                  </View>
                );
              }
              return (
                <View key={`remote-${labelKey}-${p.id}`} style={styles.photoThumbWrap}>
                  <Image
                    source={{ uri: customerConnectivitySurveyPhotoUrl(p.id), headers: token ? { Authorization: `Bearer ${token}` } : {} }}
                    style={styles.photoThumb}
                  />
                  {!readOnly ? (
                    <TouchableOpacity style={styles.photoRemove} onPress={() => removeRemotePhoto(p)}>
                      <Feather name="x" size={14} color={theme.colors.white} />
                    </TouchableOpacity>
                  ) : null}
                </View>
              );
            })}

            {list.map((p, idx) => (
              <View key={`local-${labelKey}-${idx}`} style={styles.photoThumbWrap}>
                <Image source={{ uri: p.uri }} style={styles.photoThumb} />
                {!readOnly ? (
                  <TouchableOpacity style={styles.photoRemove} onPress={() => removeLocalPhoto(labelKey, idx)}>
                    <Feather name="x" size={14} color={theme.colors.white} />
                  </TouchableOpacity>
                ) : null}
              </View>
            ))}
          </ScrollView>
        ) : (
          <Text style={styles.photoEmpty}>No attachments</Text>
        )}

        {!readOnly ? (
          <View style={styles.photoActions}>
            <TouchableOpacity style={styles.photoBtn} onPress={() => pickPhotoFor(labelKey)}>
              <Feather name="image" size={16} color={theme.colors.primary} />
              <Text style={styles.photoBtnText}>Gallery</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.photoBtn} onPress={() => capturePhotoFor(labelKey)}>
              <Feather name="camera" size={16} color={theme.colors.primary} />
              <Text style={styles.photoBtnText}>Camera</Text>
            </TouchableOpacity>
          </View>
        ) : null}
      </View>
    );
  };

  const StepGeneral = () => (
    <View style={styles.card}>
      <SectionTitle>Meta</SectionTitle>
      <Field label="Date (YYYY-MM-DD)" value={form.meta.date} onChangeText={(v) => setBlock('meta', { date: v })} placeholder="2026-06-22" />
      <Field label="Survey Performed By" value={form.meta.surveyPerformedBy} onChangeText={(v) => setBlock('meta', { surveyPerformedBy: v })} placeholder="Name" />

      <SectionTitle>Customer / Site</SectionTitle>
      <Field label="Customer Name" value={form.general.customerName} onChangeText={(v) => setBlock('general', { customerName: v })} placeholder="Customer name" />
      <Field label="Account / JC Number" value={form.general.accountOrJcNumber} onChangeText={(v) => setBlock('general', { accountOrJcNumber: v })} placeholder="Account or JC number" />
      <Field label="Site Name / Location" value={form.general.siteName} onChangeText={(v) => setBlock('general', { siteName: v })} placeholder="Site name / location" />
      <Field label="Physical Address" value={form.general.physicalAddress} onChangeText={(v) => setBlock('general', { physicalAddress: v })} placeholder="Address" />
      <Field label="Coordinates (optional)" value={form.general.coordinates} onChangeText={(v) => setBlock('general', { coordinates: v })} placeholder="lat, lng" />
      <View style={styles.row2}>
        <View style={{ flex: 1 }}>
          <Field label="Latitude" value={String(form.general.latitude ?? '')} onChangeText={(v) => setBlock('general', { latitude: v })} placeholder="e.g. -17.8" keyboardType="numeric" />
        </View>
        <View style={{ flex: 1 }}>
          <Field label="Longitude" value={String(form.general.longitude ?? '')} onChangeText={(v) => setBlock('general', { longitude: v })} placeholder="e.g. 31.0" keyboardType="numeric" />
        </View>
      </View>

      <SectionTitle>Customer Contact</SectionTitle>
      <Field label="Contact Name" value={form.general.customerContactName} onChangeText={(v) => setBlock('general', { customerContactName: v })} placeholder="Name" />
      <Field label="Phone" value={form.general.customerContactPhone} onChangeText={(v) => setBlock('general', { customerContactPhone: v })} placeholder="Phone number" keyboardType="phone-pad" />
      <Field label="Email" value={form.general.customerContactEmail} onChangeText={(v) => setBlock('general', { customerContactEmail: v })} placeholder="Email" keyboardType="email-address" />
    </View>
  );

  const StepService = () => (
    <View style={styles.card}>
      <SectionTitle>Service Requirements</SectionTitle>
      <Field label="Service Type (Fibre / Wireless / Metro-E / Other)" value={form.serviceRequirements.serviceType} onChangeText={(v) => setBlock('serviceRequirements', { serviceType: v })} placeholder="Fibre" />
      <View style={styles.row2}>
        <View style={{ flex: 1 }}>
          <Field label="Bandwidth Down (Mbps)" value={form.serviceRequirements.bandwidthDown} onChangeText={(v) => setBlock('serviceRequirements', { bandwidthDown: v })} placeholder="e.g. 50" keyboardType="numeric" />
        </View>
        <View style={{ flex: 1 }}>
          <Field label="Bandwidth Up (Mbps)" value={form.serviceRequirements.bandwidthUp} onChangeText={(v) => setBlock('serviceRequirements', { bandwidthUp: v })} placeholder="e.g. 20" keyboardType="numeric" />
        </View>
      </View>
      <Field label="Service Purpose" value={form.serviceRequirements.servicePurpose} onChangeText={(v) => setBlock('serviceRequirements', { servicePurpose: v })} placeholder="Internet / Backhaul / Corporate Link" />
      <Field label="Redundancy Required (Yes/No + details)" value={form.serviceRequirements.redundancyRequired} onChangeText={(v) => setBlock('serviceRequirements', { redundancyRequired: v })} placeholder="No" />
      <Field label="Handover Interface (RJ45 / SFP)" value={form.serviceRequirements.handoverInterface} onChangeText={(v) => setBlock('serviceRequirements', { handoverInterface: v })} placeholder="RJ45" />
      <Field label="Public IPs Required (Yes/No)" value={form.serviceRequirements.publicIpsRequired} onChangeText={(v) => setBlock('serviceRequirements', { publicIpsRequired: v })} placeholder="No" />
      <Field label="Public IP Qty" value={form.serviceRequirements.publicIpsQty} onChangeText={(v) => setBlock('serviceRequirements', { publicIpsQty: v })} placeholder="e.g. 1" keyboardType="numeric" />
      <Field label="VLAN / Routing Notes" value={form.serviceRequirements.vlanNotes} onChangeText={(v) => setBlock('serviceRequirements', { vlanNotes: v })} placeholder="Notes" />
    </View>
  );

  const StepPermissions = () => (
    <View style={styles.card}>
      <SectionTitle>Site Access & Permissions</SectionTitle>
      <Field label="Access Contact (authority)" value={form.permissions.accessContact} onChangeText={(v) => setBlock('permissions', { accessContact: v })} placeholder="Name + phone" />
      <Field label="Survey Done With" value={form.permissions.surveyDoneWith} onChangeText={(v) => setBlock('permissions', { surveyDoneWith: v })} placeholder="Name + phone" />
      <Field label="Working Hours / Restrictions" value={form.permissions.workingHours} onChangeText={(v) => setBlock('permissions', { workingHours: v })} placeholder="e.g. 08:00-17:00" />
      <Field label="Permissions Required" value={form.permissions.permissionsRequired} onChangeText={(v) => setBlock('permissions', { permissionsRequired: v })} placeholder="Wayleave / Building access / Rooftop..." />
      <Field label="Notes" value={form.permissions.notes} onChangeText={(v) => setBlock('permissions', { notes: v })} placeholder="Notes" />
    </View>
  );

  const StepOutdoor = () => (
    <View style={styles.card}>
      <SectionTitle>Outdoor Connectivity</SectionTitle>
      <Field label="Nearest POP / Node" value={form.outdoor.nearestPopNode} onChangeText={(v) => setBlock('outdoor', { nearestPopNode: v })} placeholder="POP / Node" />
      <Field label="Feeder / Switch / OLT Name" value={form.outdoor.feederSwitchOlt} onChangeText={(v) => setBlock('outdoor', { feederSwitchOlt: v })} placeholder="Switch/OLT" />
      <Field label="Free Port Available (Yes/No)" value={form.outdoor.freePortAvailable} onChangeText={(v) => setBlock('outdoor', { freePortAvailable: v })} placeholder="Yes" />
      <Field label="Port ID" value={form.outdoor.portId} onChangeText={(v) => setBlock('outdoor', { portId: v })} placeholder="Port ID" />
      <Field label="Estimated Distance (m / km)" value={form.outdoor.estimatedDistance} onChangeText={(v) => setBlock('outdoor', { estimatedDistance: v })} placeholder="e.g. 1.2 km" />
      <Field label="Route Type (Underground / Overhead / Mixed)" value={form.outdoor.routeType} onChangeText={(v) => setBlock('outdoor', { routeType: v })} placeholder="Underground" />
      <Field label="Existing Infrastructure" value={form.outdoor.existingInfrastructure} onChangeText={(v) => setBlock('outdoor', { existingInfrastructure: v })} placeholder="Ducts / Manholes / Poles / Spare fibre" />
      <Field label="Obstructions / Risks" value={form.outdoor.obstructionsRisks} onChangeText={(v) => setBlock('outdoor', { obstructionsRisks: v })} placeholder="Tar / tree cutting / private property..." />
      <Field label="Nearest Manhole / Pole Reference" value={form.outdoor.nearestManholePoleReference} onChangeText={(v) => setBlock('outdoor', { nearestManholePoleReference: v })} placeholder="Reference" />
      <Field label="Manhole / JB Details" value={form.outdoor.manholeJbDetails} onChangeText={(v) => setBlock('outdoor', { manholeJbDetails: v })} placeholder="JB size, cables, condition..." />
      <Field label="Proposed Manholes / Poles (refs)" value={form.outdoor.proposedRefs} onChangeText={(v) => setBlock('outdoor', { proposedRefs: v })} placeholder="Refs" />
    </View>
  );

  const StepIndoor = () => (
    <View style={styles.card}>
      <SectionTitle>Indoor Assessment</SectionTitle>
      <Field label="Space for Terminal Equipment (Yes/No + location)" value={form.indoor.spaceForEquipment} onChangeText={(v) => setBlock('indoor', { spaceForEquipment: v })} placeholder="Yes - Server room" />
      <Field label="Cabinet / Rack Available (Yes/No)" value={form.indoor.cabinetAvailable} onChangeText={(v) => setBlock('indoor', { cabinetAvailable: v })} placeholder="Yes" />
      <Field label="Cabinet Size / U" value={form.indoor.cabinetSize} onChangeText={(v) => setBlock('indoor', { cabinetSize: v })} placeholder="e.g. 9U" />
      <Field label="New Cabinet Required (Yes/No)" value={form.indoor.newCabinetRequired} onChangeText={(v) => setBlock('indoor', { newCabinetRequired: v })} placeholder="No" />
      <Field label="Power Available (Yes/No)" value={form.indoor.powerAvailable} onChangeText={(v) => setBlock('indoor', { powerAvailable: v })} placeholder="Yes" />
      <Field label="Socket Type (Round/Square)" value={form.indoor.socketType} onChangeText={(v) => setBlock('indoor', { socketType: v })} placeholder="Square" />
      <Field label="Distance to Socket (m)" value={form.indoor.distanceToSocket} onChangeText={(v) => setBlock('indoor', { distanceToSocket: v })} placeholder="e.g. 5" keyboardType="numeric" />
      <Field label="Back-up Power (UPS/Gen)" value={form.indoor.backupPower} onChangeText={(v) => setBlock('indoor', { backupPower: v })} placeholder="UPS" />
      <Field label="Air-conditioning (Yes/No)" value={form.indoor.airConditioning} onChangeText={(v) => setBlock('indoor', { airConditioning: v })} placeholder="Yes" />
      <Field label="Earthing" value={form.indoor.earthing} onChangeText={(v) => setBlock('indoor', { earthing: v })} placeholder="Available / To be installed" />
      <Field label="Internal Cabling Route" value={form.indoor.internalCablingRoute} onChangeText={(v) => setBlock('indoor', { internalCablingRoute: v })} placeholder="Trunking / Conduit / Ceiling" />
      <Field label="Notes" value={form.indoor.notes} onChangeText={(v) => setBlock('indoor', { notes: v })} placeholder="Notes" />
    </View>
  );

  const StepPhotos = () => (
    <View style={styles.card}>
      <SectionTitle>Photos Checklist</SectionTitle>
      <PhotoCard labelKey="building_entry" title="Building entry access point" />
      <PhotoCard labelKey="cabinet_space" title="Equipment space / cabinet location" />
      <PhotoCard labelKey="nearest_manhole_pole" title="Nearest manhole / pole / duct access" />
      <PhotoCard labelKey="route_obstacles" title="Route obstacles" />
      <PhotoCard labelKey="power_point" title="Power connection point" />
      <PhotoCard labelKey="indoor_route" title="Indoor cable route" />
      <PhotoCard labelKey="termination_point" title="Termination point mounting location" />
      <PhotoCard labelKey="route_sketch" title="Route sketch with measurements" />
    </View>
  );

  const StepReview = () => (
    <View style={styles.card}>
      <SectionTitle>Review</SectionTitle>
      <Text style={styles.reviewLine}>Customer: {form.general.customerName || '-'}</Text>
      <Text style={styles.reviewLine}>Account/JC: {form.general.accountOrJcNumber || '-'}</Text>
      <Text style={styles.reviewLine}>Site: {form.general.siteName || '-'}</Text>
      <Text style={styles.reviewLine}>Address: {form.general.physicalAddress || '-'}</Text>
      <Text style={styles.reviewLine}>Service Type: {form.serviceRequirements.serviceType || '-'}</Text>
      <Text style={styles.reviewLine}>Bandwidth: {form.serviceRequirements.bandwidthDown || '-'} / {form.serviceRequirements.bandwidthUp || '-'} Mbps</Text>
      <Text style={styles.reviewLine}>POP/Node: {form.outdoor.nearestPopNode || '-'}</Text>
      <Text style={styles.reviewLine}>Free Port: {form.outdoor.freePortAvailable || '-'}</Text>
      <Text style={styles.reviewLine}>Indoor Power: {form.indoor.powerAvailable || '-'}</Text>
      <Text style={styles.reviewHint}>Use Draft to save incomplete work. Use Submit when ready.</Text>
    </View>
  );

  const StepBody = () => {
    const key = steps[step]?.key;
    if (key === 'general') return <StepGeneral />;
    if (key === 'service') return <StepService />;
    if (key === 'permissions') return <StepPermissions />;
    if (key === 'outdoor') return <StepOutdoor />;
    if (key === 'indoor') return <StepIndoor />;
    if (key === 'photos') return <StepPhotos />;
    return <StepReview />;
  };

  if (loadingRemote) {
    return (
      <SafeAreaView style={styles.container} edges={['bottom']}>
        <View style={styles.center}>
          <ActivityIndicator color={theme.colors.primary} />
          <Text style={styles.centerText}>Loading survey...</Text>
        </View>
      </SafeAreaView>
    );
  }

  if (readOnly) {
    return (
      <SafeAreaView style={styles.container} edges={['bottom']}>
        <View style={styles.center}>
          <Feather name="lock" size={22} color={theme.colors.secondaryText} />
          <Text style={styles.centerText}>You don't have permission to {isEdit ? 'edit' : 'create'} surveys.</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <View style={styles.topBar}>
        <View style={{ flex: 1 }}>
          <Text style={styles.topTitle}>{currentTitle}</Text>
          <Text style={styles.topSub}>{step + 1} / {totalSteps}</Text>
        </View>
        <TouchableOpacity style={styles.stepPill} onPress={() => setStep(0)}>
          <Feather name="list" size={16} color={theme.colors.primary} />
          <Text style={styles.stepPillText}>Start</Text>
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scroll}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={() => {}} tintColor={theme.colors.primary} />}
      >
        <StepBody />
      </ScrollView>

      <View style={styles.bottomBar}>
        <TouchableOpacity
          style={[styles.navBtn, step === 0 && styles.navBtnDisabled]}
          disabled={step === 0}
          onPress={() => setStep((s) => Math.max(0, s - 1))}
        >
          <Feather name="chevron-left" size={18} color={step === 0 ? theme.colors.muted : theme.colors.text} />
          <Text style={[styles.navBtnText, step === 0 && styles.navBtnTextDisabled]}>Back</Text>
        </TouchableOpacity>

        {step < totalSteps - 1 ? (
          <TouchableOpacity style={styles.nextBtn} onPress={() => setStep((s) => Math.min(totalSteps - 1, s + 1))}>
            <Text style={styles.nextBtnText}>Next</Text>
            <Feather name="chevron-right" size={18} color={theme.colors.white} />
          </TouchableOpacity>
        ) : (
          <View style={styles.submitRow}>
            <TouchableOpacity style={styles.draftBtn} onPress={() => submit('draft')}>
              <Feather name="save" size={16} color={theme.colors.primary} />
              <Text style={styles.draftBtnText}>Draft</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.submitBtn} onPress={() => submit('submitted')}>
              <Feather name="check" size={16} color={theme.colors.white} />
              <Text style={styles.submitBtnText}>Submit</Text>
            </TouchableOpacity>
          </View>
        )}
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  center: { flex: 1, alignItems: 'center', justifyContent: 'center', gap: 10, padding: theme.spacing.lg },
  centerText: { color: theme.colors.secondaryText, textAlign: 'center' },
  topBar: {
    paddingHorizontal: theme.spacing.lg,
    paddingTop: theme.spacing.md,
    paddingBottom: theme.spacing.sm,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  topTitle: { fontSize: theme.fontSizes.lg, fontWeight: '800', color: theme.colors.text },
  topSub: { marginTop: 2, fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText },
  stepPill: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: 'rgba(59,130,246,0.08)',
    borderWidth: 1,
    borderColor: 'rgba(59,130,246,0.25)',
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: theme.borderRadius.circle,
  },
  stepPillText: { color: theme.colors.primary, fontWeight: '800' },
  scroll: { padding: theme.spacing.lg, paddingBottom: theme.spacing.xxl },
  card: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  sectionTitle: { marginTop: 6, marginBottom: 10, fontWeight: '800', color: theme.colors.text, fontSize: theme.fontSizes.md },
  field: { marginBottom: 12 },
  fieldLabel: { marginBottom: 6, color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, fontWeight: '700' },
  input: {
    backgroundColor: theme.colors.background,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    paddingHorizontal: 12,
    paddingVertical: 10,
    color: theme.colors.text,
    fontSize: theme.fontSizes.sm,
  },
  row2: { flexDirection: 'row', gap: 10 },
  bottomBar: {
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.sm,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
    backgroundColor: theme.colors.background,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 10,
  },
  navBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.surface,
  },
  navBtnDisabled: { opacity: 0.6 },
  navBtnText: { fontWeight: '800', color: theme.colors.text },
  navBtnTextDisabled: { color: theme.colors.muted },
  nextBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: theme.colors.primary,
    paddingVertical: 12,
    paddingHorizontal: 14,
    borderRadius: theme.borderRadius.md,
  },
  nextBtnText: { fontWeight: '800', color: theme.colors.white },
  submitRow: { flexDirection: 'row', gap: 10 },
  draftBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    borderWidth: 1,
    borderColor: 'rgba(59,130,246,0.35)',
    backgroundColor: 'rgba(59,130,246,0.08)',
    paddingVertical: 12,
    paddingHorizontal: 14,
    borderRadius: theme.borderRadius.md,
  },
  draftBtnText: { fontWeight: '800', color: theme.colors.primary },
  submitBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: theme.colors.success,
    paddingVertical: 12,
    paddingHorizontal: 14,
    borderRadius: theme.borderRadius.md,
  },
  submitBtnText: { fontWeight: '800', color: theme.colors.white },
  photoCard: {
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.sm,
    marginBottom: theme.spacing.md,
    backgroundColor: theme.colors.surface,
  },
  photoTitle: { fontWeight: '800', color: theme.colors.text },
  photoStrip: { marginTop: 10, gap: 10, paddingRight: 6 },
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
  photoRemove: {
    position: 'absolute',
    top: 6,
    right: 6,
    width: 22,
    height: 22,
    borderRadius: 11,
    backgroundColor: 'rgba(0,0,0,0.6)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  photoActions: { flexDirection: 'row', gap: 10, marginTop: 10 },
  photoBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.background,
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: theme.borderRadius.md,
  },
  photoBtnText: { color: theme.colors.primary, fontWeight: '800' },
  photoEmpty: { marginTop: 8, color: theme.colors.secondaryText },
  reviewLine: { marginTop: 10, color: theme.colors.text, fontWeight: '700' },
  reviewHint: { marginTop: 14, color: theme.colors.secondaryText },
});
