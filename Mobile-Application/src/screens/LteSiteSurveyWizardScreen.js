import React, { useContext, useEffect, useMemo, useRef, useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  Alert,
  Image,
  Platform,
  KeyboardAvoidingView,
  Modal,
} from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as ImagePicker from 'expo-image-picker';
import { Feather } from '@expo/vector-icons';
import { createLteSiteSurvey, deleteLteSurveyPhoto, getAuthToken, getLteEnabledUsers, getLteSiteSurvey, lteSurveyPhotoUrl, updateLteSiteSurvey } from '../services/api';
import { UserContext } from '../context/UserContext';
import { theme } from '../styles/theme';

const DRAFT_KEY = 'lte_site_survey_draft_v1';

const defaultMaterials = {
  civils: [
    { description: 'Fibre Cable', unit: 'm', qty: '' },
    { description: 'PVC Trunking', unit: 'm', qty: '' },
    { description: 'Manholes', unit: 'ea', qty: '' },
    { description: 'Trenching Normal Ground', unit: 'm', qty: '' },
    { description: 'Trenching Gravel', unit: 'm', qty: '' },
    { description: 'Total Trenching (HDPE Ducts)', unit: 'm', qty: '' },
    { description: 'Length requiring Wayleaves', unit: 'm', qty: '' },
    { description: 'Steel Pipes', unit: 'm', qty: '' },
    { description: 'PVC pipes (90mm)', unit: 'm', qty: '' },
    { description: 'Poles', unit: 'ea', qty: '' },
    { description: 'Tar', unit: 'm', qty: '' },
    { description: 'Plinth to be constructed', unit: 'm³', qty: '' },
    { description: 'Grounding System', unit: 'm', qty: '' },
    { description: 'Commercial Power Cable', unit: 'm', qty: '' },
    { description: 'Distribution Board', unit: 'ea', qty: '' },
  ],
  nte: [
    { description: 'SFP modules', unit: 'ea', qty: '' },
    { description: 'Convertors', unit: 'ea', qty: '' },
    { description: 'UTP Cable', unit: 'm', qty: '' },
    { description: 'RJ45 Connectors', unit: 'ea', qty: '' },
    { description: 'Switch', unit: 'ea', qty: '' },
    { description: 'Access Points', unit: 'ea', qty: '' },
    { description: '3m sc-sc patch cord', unit: 'ea', qty: '' },
    { description: 'Patch panel', unit: 'ea', qty: '' },
    { description: 'SM midi-couplers', unit: 'ea', qty: '' },
    { description: 'ST Connectors', unit: 'ea', qty: '' },
    { description: 'Pig tails', unit: 'ea', qty: '' },
    { description: 'Splice Protectors', unit: 'ea', qty: '' },
    { description: 'Dome Boxes way', unit: 'ea', qty: '' },
    { description: 'Cabinet', unit: 'ea', qty: '' },
  ],
};

const emptyForm = {
  meta: {
    date: '',
    surveyPerformedByUserId: '',
    surveyPerformedBy: '',
  },
  general: {
    siteName: '',
    jcNumber: '',
    coordinates: '',
    latitude: '',
    longitude: '',
    physicalAddress: '',
    provinceRegion: '',
    contactDetails: '',
  },
  accessSecurity: {
    securityFenceAvailable: false,
    conditionOfFence: '',
    siteAccess24h: false,
    guardAvailable: false,
    lineOfSightAvailability: false,
  },
  tower: {
    terrainType: '',
    towerOwner: '',
    allocatedHeight: '',
  },
  transmission: {
    nearestManholeCoordinates: '',
    distanceFromExistingFibre: '',
    distanceFromNearestPop: '',
    distanceFromNearestPop2: '',
    allocatedPort: '',
    requiredBackhaulCapacity: '',
    backhaulType: '',
  },
  power: {
    powerSourceType: '',
    phase: '',
    inputVoltage: '',
    batteryCapacity: '',
    batteryAutonomyHrs: '',
    earthingSystemInstalled: '',
    cableUtilitySourceToSite: '',
    conditionOfDb: '',
  },
  civilWorks: {
    trenchingRequired: false,
    breakingConcreteTar: false,
    polePlantingRequired: false,
    constructionOfPlinth: false,
    newManholeRequired: false,
  },
  materials: defaultMaterials,
  notes: {
    notes: '',
  },
  photos: {
    nearest_joint_box: [],
    fibre_route_towards_tower: [],
    tower_overview: [],
    new_plinth_space: [],
    power_connection_image: [],
    termination_point_image: [],
    route_sketch: [],
  },
};

function Label({ children }) {
  return <Text style={styles.label}>{children}</Text>;
}

function Field({ label, value, onChangeText, placeholder, multiline, keyboardType, editable = true }) {
  return (
    <View style={{ marginBottom: theme.spacing.md }}>
      <Label>{label}</Label>
      <TextInput
        style={[styles.input, multiline && styles.textarea, !editable && styles.inputDisabled]}
        placeholder={placeholder}
        placeholderTextColor={theme.colors.muted}
        value={value}
        onChangeText={onChangeText}
        multiline={multiline}
        numberOfLines={multiline ? 4 : 1}
        textAlignVertical={multiline ? 'top' : 'center'}
        keyboardType={keyboardType}
        editable={editable}
      />
    </View>
  );
}

function SelectField({ label, value, placeholder, onPress, disabled }) {
  return (
    <View style={{ marginBottom: theme.spacing.md }}>
      <Label>{label}</Label>
      <TouchableOpacity
        style={[styles.selectInput, disabled && styles.inputDisabled]}
        onPress={onPress}
        disabled={disabled}
      >
        <Text style={[styles.selectText, !value && { color: theme.colors.muted }]} numberOfLines={1}>
          {value || placeholder || 'Select'}
        </Text>
        <Feather name="chevron-down" size={18} color={theme.colors.secondaryText} />
      </TouchableOpacity>
    </View>
  );
}

function PillOptions({ label, options, value, onChange, disabled }) {
  return (
    <View style={{ marginBottom: theme.spacing.md }}>
      <Label>{label}</Label>
      <View style={styles.pillsRow}>
        {options.map((opt) => {
          const selected = value === opt.value;
          return (
            <TouchableOpacity
              key={opt.value}
              style={[styles.pill, selected && styles.pillSelected, disabled && styles.pillDisabled]}
              onPress={() => !disabled && onChange(opt.value)}
              disabled={disabled}
            >
              <Text style={[styles.pillText, selected && styles.pillTextSelected]}>{opt.label}</Text>
            </TouchableOpacity>
          );
        })}
      </View>
    </View>
  );
}

function CheckRow({ label, checked, onToggle, disabled }) {
  return (
    <TouchableOpacity style={[styles.checkRow, disabled && { opacity: 0.7 }]} onPress={onToggle} disabled={disabled}>
      <View style={[styles.checkbox, checked && styles.checkboxChecked]}>
        {checked ? <Feather name="check" size={14} color={theme.colors.white} /> : null}
      </View>
      <Text style={styles.checkLabel}>{label}</Text>
    </TouchableOpacity>
  );
}

function SectionTitle({ children }) {
  return <Text style={styles.sectionTitle}>{children}</Text>;
}

export default function LteSiteSurveyWizardScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { user } = useContext(UserContext);
  const insets = useSafeAreaInsets();
  const surveyId = route?.params?.surveyId || route?.params?.fromSurveyId;
  const mode = route?.params?.mode || (surveyId ? 'view' : 'create');
  const readOnly = mode === 'view';
  const isEdit = mode === 'edit';

  const [step, setStep] = useState(0);
  const [loading, setLoading] = useState(false);
  const [loadingRemote, setLoadingRemote] = useState(false);
  const [form, setForm] = useState(emptyForm);
  const [remotePhotos, setRemotePhotos] = useState([]);
  const [usersOpen, setUsersOpen] = useState(false);
  const [enabledUsers, setEnabledUsers] = useState([]);
  const token = getAuthToken();

  const saveTimer = useRef(null);

  const steps = useMemo(() => ([
    { key: 'general', title: 'General Site Information' },
    { key: 'coordinates', title: 'Coordinates' },
    { key: 'access', title: 'Access + Tower' },
    { key: 'transmission', title: 'Transmission' },
    { key: 'power', title: 'Power' },
    { key: 'civil', title: 'Civil Works' },
    { key: 'materials', title: 'Materials' },
    { key: 'notes', title: 'Notes' },
    { key: 'photos', title: 'Images & Attachments' },
    { key: 'review', title: 'Overview' },
  ]), []);

  const totalSteps = steps.length;
  const currentTitle = steps[step]?.title || 'LTE Site Survey';
  const progress = totalSteps > 1 ? (step + 1) / totalSteps : 1;

  useEffect(() => {
    navigation.setOptions({ title: 'LTE Site Survey' });
  }, []);

  useEffect(() => {
    if (mode === 'view') return;
    let mounted = true;
    (async () => {
      try {
        const res = await getLteEnabledUsers();
        const data = res?.data;
        if (!mounted) return;
        setEnabledUsers(Array.isArray(data) ? data : []);
      } catch (e) {
        if (mounted) setEnabledUsers([]);
      }
    })();
    return () => { mounted = false; };
  }, [mode]);

  useEffect(() => {
    if (mode !== 'create') return;
    const name = user?.name ? String(user.name) : '';
    if (!name) return;
    setForm((prev) => {
      if (prev?.meta?.surveyPerformedBy) return prev;
      return { ...prev, meta: { ...(prev.meta || {}), surveyPerformedByUserId: String(user?.id || ''), surveyPerformedBy: name } };
    });
  }, [user, mode]);

  useEffect(() => {
    if (mode !== 'create') return;

    let mounted = true;
    (async () => {
      try {
        const raw = await AsyncStorage.getItem(DRAFT_KEY);
        if (!mounted) return;
        if (raw) {
          const parsed = JSON.parse(raw);
          if (parsed && typeof parsed === 'object') {
            setForm((prev) => ({
              ...prev,
              ...parsed,
              materials: parsed.materials || defaultMaterials,
              photos: { ...prev.photos, ...(parsed.photos || {}) },
            }));
          }
        }
      } catch (e) {
      }
    })();

    return () => { mounted = false; };
  }, [mode]);

  useEffect(() => {
    if (!surveyId) return;
    let mounted = true;
    setLoadingRemote(true);
    (async () => {
      try {
        const res = await getLteSiteSurvey(surveyId);
        const data = res?.data;
        const payload = data?.payload;
        if (!mounted) return;
        if (payload && typeof payload === 'object') {
          const lat = payload?.general?.latitude ?? data?.latitude ?? '';
          const lng = payload?.general?.longitude ?? data?.longitude ?? '';
          setForm((prev) => ({
            ...prev,
            ...payload,
            materials: payload.materials || defaultMaterials,
            photos: { ...prev.photos },
            notes: payload.notes || prev.notes,
            general: {
              ...(prev.general || {}),
              ...(payload.general || {}),
              latitude: lat === null ? '' : String(lat),
              longitude: lng === null ? '' : String(lng),
            },
          }));
        }
        const photos = Array.isArray(data?.photos) ? data.photos : [];
        setRemotePhotos(photos);
      } catch (e) {
        if (mounted) Alert.alert('Error', 'Failed to load survey.');
      } finally {
        if (mounted) setLoadingRemote(false);
      }
    })();
    return () => { mounted = false; };
  }, [surveyId]);

  useEffect(() => {
    if (mode !== 'create') return;
    if (saveTimer.current) clearTimeout(saveTimer.current);
    saveTimer.current = setTimeout(async () => {
      try {
        await AsyncStorage.setItem(DRAFT_KEY, JSON.stringify(form));
      } catch (e) {
      }
    }, 500);
    return () => {
      if (saveTimer.current) clearTimeout(saveTimer.current);
    };
  }, [form, mode]);

  const updateSection = (section, key, value) => {
    setForm((prev) => ({
      ...prev,
      [section]: { ...(prev[section] || {}), [key]: value },
    }));
  };

  const toggle = (section, key) => {
    setForm((prev) => ({
      ...prev,
      [section]: { ...(prev[section] || {}), [key]: !prev?.[section]?.[key] },
    }));
  };

  const updateMaterialRow = (kind, index, key, value) => {
    setForm((prev) => {
      const rows = Array.isArray(prev?.materials?.[kind]) ? prev.materials[kind] : [];
      const next = rows.map((r, i) => (i === index ? { ...r, [key]: value } : r));
      return { ...prev, materials: { ...(prev.materials || defaultMaterials), [kind]: next } };
    });
  };

  const addMaterialRow = (kind) => {
    setForm((prev) => {
      const rows = Array.isArray(prev?.materials?.[kind]) ? prev.materials[kind] : [];
      return { ...prev, materials: { ...(prev.materials || defaultMaterials), [kind]: [...rows, { description: '', unit: '', qty: '' }] } };
    });
  };

  const removeMaterialRow = (kind, index) => {
    setForm((prev) => {
      const rows = Array.isArray(prev?.materials?.[kind]) ? prev.materials[kind] : [];
      return { ...prev, materials: { ...(prev.materials || defaultMaterials), [kind]: rows.filter((_, i) => i !== index) } };
    });
  };

  const getPhotoList = (label, prev) => {
    const value = (prev?.photos || {})[label];
    return Array.isArray(value) ? value : (value ? [value] : []);
  };

  const addPhotos = (label, files) => {
    const list = Array.isArray(files) ? files.filter(Boolean) : [];
    if (list.length === 0) return;
    setForm((prev) => {
      const current = getPhotoList(label, prev);
      return { ...prev, photos: { ...(prev.photos || {}), [label]: [...current, ...list] } };
    });
  };

  const removePhotoAt = (label, index) => {
    setForm((prev) => {
      const current = getPhotoList(label, prev);
      return { ...prev, photos: { ...(prev.photos || {}), [label]: current.filter((_, i) => i !== index) } };
    });
  };

  const clearPhotos = (label) => {
    setForm((prev) => ({ ...prev, photos: { ...(prev.photos || {}), [label]: [] } }));
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
            const res = await deleteLteSurveyPhoto(id);
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

  const clearDraft = async () => {
    setForm(emptyForm);
    setStep(0);
    try {
      await AsyncStorage.removeItem(DRAFT_KEY);
    } catch (e) {
    }
  };

  const validateForSubmit = () => {
    const siteName = form?.general?.siteName?.trim();
    if (!siteName) return 'Site Name is required.';
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
        accessSecurity: form.accessSecurity,
        tower: form.tower,
        transmission: form.transmission,
        power: form.power,
        civilWorks: form.civilWorks,
        materials: form.materials,
        notes: form.notes,
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

      const res = isEdit ? await updateLteSiteSurvey(surveyId, fd) : await createLteSiteSurvey(fd);
      if (!res?.success) {
        Alert.alert('Error', res?.message || 'Failed to save survey.');
        return;
      }

      if (status === 'submitted') {
        if (mode === 'create') await AsyncStorage.removeItem(DRAFT_KEY);
        Alert.alert('Success', isEdit ? 'Survey updated and submitted.' : 'Survey submitted.', [
          {
            text: 'OK',
            onPress: () => {
              if (isEdit) {
                navigation.navigate('LteSiteSurveyView', { id: surveyId });
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
                    source={{ uri: lteSurveyPhotoUrl(p.id), headers: token ? { Authorization: `Bearer ${token}` } : {} }}
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
            {list.map((f, idx) => (
              <View key={`${labelKey}-${idx}`} style={styles.photoThumbWrap}>
                <Image source={{ uri: f.uri }} style={styles.photoThumb} />
                {!readOnly ? (
                  <TouchableOpacity style={styles.photoRemove} onPress={() => removePhotoAt(labelKey, idx)}>
                    <Feather name="x" size={14} color={theme.colors.white} />
                  </TouchableOpacity>
                ) : null}
              </View>
            ))}
          </ScrollView>
        ) : (
          <View style={styles.photoEmpty}>
            <Feather name="image" size={18} color={theme.colors.secondaryText} />
            <Text style={styles.photoEmptyText}>No photo</Text>
          </View>
        )}
        <View style={styles.photoActions}>
          <TouchableOpacity style={[styles.photoBtn, readOnly && styles.disabledBtn]} onPress={() => pickPhotoFor(labelKey)} disabled={readOnly}>
            <Feather name="image" size={16} color={theme.colors.primary} />
            <Text style={styles.photoBtnText}>Gallery</Text>
          </TouchableOpacity>
          <TouchableOpacity style={[styles.photoBtn, readOnly && styles.disabledBtn]} onPress={() => capturePhotoFor(labelKey)} disabled={readOnly}>
            <Feather name="camera" size={16} color={theme.colors.primary} />
            <Text style={styles.photoBtnText}>Camera</Text>
          </TouchableOpacity>
          {!readOnly && list.length > 0 ? (
            <TouchableOpacity style={styles.photoBtnDanger} onPress={() => clearPhotos(labelKey)}>
              <Feather name="trash-2" size={16} color={theme.colors.white} />
              <Text style={styles.photoBtnDangerText}>Clear</Text>
            </TouchableOpacity>
          ) : null}
        </View>
      </View>
    );
  };

  const renderStep = () => {
    const editable = !readOnly && !loading && !loadingRemote;

    if (loadingRemote) {
      return (
        <View style={styles.center}>
          <ActivityIndicator color={theme.colors.primary} />
          <Text style={styles.centerText}>Loading survey...</Text>
        </View>
      );
    }

    if (step === 0) {
      return (
        <>
          <SectionTitle>Meta</SectionTitle>
          <Field
            label="Date"
            value={form.meta.date}
            onChangeText={(v) => updateSection('meta', 'date', v)}
            placeholder="YYYY-MM-DD"
            editable={editable}
          />
          <SelectField
            label="Survey Performed By"
            value={form.meta.surveyPerformedBy}
            placeholder="Select user"
            onPress={() => setUsersOpen(true)}
            disabled={!editable}
          />

          <SectionTitle>General Site Information</SectionTitle>
          <Field
            label="Site Name"
            value={form.general.siteName}
            onChangeText={(v) => updateSection('general', 'siteName', v)}
            placeholder="Site name"
            editable={editable}
          />
          <Field
            label="JC Number"
            value={form.general.jcNumber}
            onChangeText={(v) => updateSection('general', 'jcNumber', v)}
            placeholder="JC number"
            editable={editable}
          />
          <Field
            label="Province/Region"
            value={form.general.provinceRegion}
            onChangeText={(v) => updateSection('general', 'provinceRegion', v)}
            placeholder="Province/Region"
            editable={editable}
          />
          <Field
            label="Physical Address"
            value={form.general.physicalAddress}
            onChangeText={(v) => updateSection('general', 'physicalAddress', v)}
            placeholder="Address"
            multiline
            editable={editable}
          />
          <Field
            label="Contact Details"
            value={form.general.contactDetails}
            onChangeText={(v) => updateSection('general', 'contactDetails', v)}
            placeholder="Name, phone, email"
            multiline
            editable={editable}
          />
        </>
      );
    }

    if (step === 1) {
      const lat = form?.general?.latitude ?? '';
      const lng = form?.general?.longitude ?? '';
      return (
        <>
          <SectionTitle>Coordinates</SectionTitle>
          <Field
            label="Latitude"
            value={String(lat)}
            onChangeText={(v) => {
              updateSection('general', 'latitude', v);
              const nextLat = String(v || '').trim();
              const nextLng = String(lng || '').trim();
              if (nextLat !== '' && nextLng !== '' && !isNaN(Number(nextLat)) && !isNaN(Number(nextLng))) {
                updateSection('general', 'coordinates', `${nextLat}, ${nextLng}`);
              }
            }}
            placeholder="e.g. -17.8292"
            keyboardType="numeric"
            editable={editable}
          />
          <Field
            label="Longitude"
            value={String(lng)}
            onChangeText={(v) => {
              updateSection('general', 'longitude', v);
              const nextLat = String(lat || '').trim();
              const nextLng = String(v || '').trim();
              if (nextLat !== '' && nextLng !== '' && !isNaN(Number(nextLat)) && !isNaN(Number(nextLng))) {
                updateSection('general', 'coordinates', `${nextLat}, ${nextLng}`);
              }
            }}
            placeholder="e.g. 31.0522"
            keyboardType="numeric"
            editable={editable}
          />
          <Field
            label="Coordinates (auto)"
            value={form.general.coordinates}
            onChangeText={(v) => updateSection('general', 'coordinates', v)}
            placeholder="e.g. -17.8292, 31.0522"
            editable={editable}
          />
        </>
      );
    }

    if (step === 2) {
      return (
        <>
          <SectionTitle>Site Access and Security</SectionTitle>
          <CheckRow
            label="Security Fence Available"
            checked={!!form.accessSecurity.securityFenceAvailable}
            onToggle={() => toggle('accessSecurity', 'securityFenceAvailable')}
            disabled={!editable}
          />
          <PillOptions
            label="Condition of Fence"
            value={form.accessSecurity.conditionOfFence}
            onChange={(v) => updateSection('accessSecurity', 'conditionOfFence', v)}
            options={[
              { label: 'Good', value: 'good' },
              { label: 'Bad', value: 'bad' },
              { label: 'Not Available', value: 'not_available' },
            ]}
            disabled={!editable}
          />
          <CheckRow
            label="24 Hour Site Access"
            checked={!!form.accessSecurity.siteAccess24h}
            onToggle={() => toggle('accessSecurity', 'siteAccess24h')}
            disabled={!editable}
          />
          <CheckRow
            label="Guard Available"
            checked={!!form.accessSecurity.guardAvailable}
            onToggle={() => toggle('accessSecurity', 'guardAvailable')}
            disabled={!editable}
          />
          <CheckRow
            label="Line of Sight Availability"
            checked={!!form.accessSecurity.lineOfSightAvailability}
            onToggle={() => toggle('accessSecurity', 'lineOfSightAvailability')}
            disabled={!editable}
          />

          <SectionTitle>Tower / Structural Details</SectionTitle>
          <PillOptions
            label="Terrain Type"
            value={form.tower.terrainType}
            onChange={(v) => updateSection('tower', 'terrainType', v)}
            options={[
              { label: 'Hilltop', value: 'hilltop' },
              { label: 'Elevated', value: 'elevated_ground' },
              { label: 'Flat', value: 'flat_terrain' },
              { label: 'Valley', value: 'valley' },
              { label: 'Slope', value: 'mountain_slope' },
              { label: 'Rooftop', value: 'urban_rooftop' },
              { label: 'Other', value: 'other' },
            ]}
            disabled={!editable}
          />
          <Field
            label="Tower Owner"
            value={form.tower.towerOwner}
            onChangeText={(v) => updateSection('tower', 'towerOwner', v)}
            placeholder="Tower owner"
            editable={editable}
          />
          <Field
            label="Allocated Height"
            value={form.tower.allocatedHeight}
            onChangeText={(v) => updateSection('tower', 'allocatedHeight', v)}
            placeholder="e.g. 30m"
            editable={editable}
          />
        </>
      );
    }

    if (step === 3) {
      return (
        <>
          <SectionTitle>Transmission Details</SectionTitle>
          <Field
            label="Coordinates of nearest manhole"
            value={form.transmission.nearestManholeCoordinates}
            onChangeText={(v) => updateSection('transmission', 'nearestManholeCoordinates', v)}
            placeholder="Co-ordinates"
            editable={editable}
          />
          <Field
            label="Distance from existing fibre"
            value={form.transmission.distanceFromExistingFibre}
            onChangeText={(v) => updateSection('transmission', 'distanceFromExistingFibre', v)}
            placeholder="e.g. 2km"
            editable={editable}
          />
          <Field
            label="POP Name"
            value={form.transmission.distanceFromNearestPop}
            onChangeText={(v) => updateSection('transmission', 'distanceFromNearestPop', v)}
            placeholder="e.g. 5km"
            editable={editable}
          />
          <Field
            label="Distance from POP"
            value={form.transmission.distanceFromNearestPop2}
            onChangeText={(v) => updateSection('transmission', 'distanceFromNearestPop2', v)}
            placeholder="Optional"
            editable={editable}
          />
          <Field
            label="Allocated Port"
            value={form.transmission.allocatedPort}
            onChangeText={(v) => updateSection('transmission', 'allocatedPort', v)}
            placeholder="Port"
            editable={editable}
          />
          <Field
            label="Required Backhaul Capacity"
            value={form.transmission.requiredBackhaulCapacity}
            onChangeText={(v) => updateSection('transmission', 'requiredBackhaulCapacity', v)}
            placeholder="e.g. 1Gbps"
            editable={editable}
          />
          <PillOptions
            label="Backhaul Type"
            value={form.transmission.backhaulType}
            onChange={(v) => updateSection('transmission', 'backhaulType', v)}
            options={[
              { label: 'Fibre', value: 'fibre' },
              { label: 'Microwave', value: 'microwave' },
            ]}
            disabled={!editable}
          />
        </>
      );
    }

    if (step === 4) {
      return (
        <>
          <SectionTitle>Power Details</SectionTitle>
          <PillOptions
            label="Power Source Type"
            value={form.power.powerSourceType}
            onChange={(v) => updateSection('power', 'powerSourceType', v)}
            options={[
              { label: 'ZESA', value: 'zesa' },
              { label: 'Generator', value: 'generator' },
              { label: 'Solar', value: 'solar' },
              { label: 'Other', value: 'other' },
            ]}
            disabled={!editable}
          />
          <PillOptions
            label="Single/Three Phase"
            value={form.power.phase}
            onChange={(v) => updateSection('power', 'phase', v)}
            options={[
              { label: 'Single', value: 'single_phase' },
              { label: 'Three', value: 'three_phase' },
            ]}
            disabled={!editable}
          />
          <Field
            label="Input Voltage"
            value={form.power.inputVoltage}
            onChangeText={(v) => updateSection('power', 'inputVoltage', v)}
            placeholder="e.g. 220V"
            editable={editable}
          />
          <Field
            label="Battery Capacity"
            value={form.power.batteryCapacity}
            onChangeText={(v) => updateSection('power', 'batteryCapacity', v)}
            placeholder="e.g. 200Ah"
            editable={editable}
          />
          <Field
            label="Battery Autonomy (hrs)"
            value={form.power.batteryAutonomyHrs}
            onChangeText={(v) => updateSection('power', 'batteryAutonomyHrs', v)}
            placeholder="e.g. 6"
            keyboardType="numeric"
            editable={editable}
          />
          <PillOptions
            label="Earthing System Installed"
            value={form.power.earthingSystemInstalled}
            onChange={(v) => updateSection('power', 'earthingSystemInstalled', v)}
            options={[
              { label: 'Available', value: 'available' },
              { label: 'Not Available', value: 'not_available' },
            ]}
            disabled={!editable}
          />
          <PillOptions
            label="Cable from Utility Source to Site"
            value={form.power.cableUtilitySourceToSite}
            onChange={(v) => updateSection('power', 'cableUtilitySourceToSite', v)}
            options={[
              { label: 'Available', value: 'available' },
              { label: 'Not Available', value: 'not_available' },
            ]}
            disabled={!editable}
          />
          <PillOptions
            label="Condition of DB"
            value={form.power.conditionOfDb}
            onChange={(v) => updateSection('power', 'conditionOfDb', v)}
            options={[
              { label: 'Good', value: 'good' },
              { label: 'Bad', value: 'bad' },
              { label: 'Not Available', value: 'not_available' },
            ]}
            disabled={!editable}
          />
        </>
      );
    }

    if (step === 5) {
      return (
        <>
          <SectionTitle>Civil Works Requirement</SectionTitle>
          <CheckRow
            label="Trenching Required"
            checked={!!form.civilWorks.trenchingRequired}
            onToggle={() => toggle('civilWorks', 'trenchingRequired')}
            disabled={!editable}
          />
          <CheckRow
            label="Breaking Concrete/Tar"
            checked={!!form.civilWorks.breakingConcreteTar}
            onToggle={() => toggle('civilWorks', 'breakingConcreteTar')}
            disabled={!editable}
          />
          <CheckRow
            label="Pole Planting Required"
            checked={!!form.civilWorks.polePlantingRequired}
            onToggle={() => toggle('civilWorks', 'polePlantingRequired')}
            disabled={!editable}
          />
          <CheckRow
            label="Construction of Plinth"
            checked={!!form.civilWorks.constructionOfPlinth}
            onToggle={() => toggle('civilWorks', 'constructionOfPlinth')}
            disabled={!editable}
          />
          <CheckRow
            label="New Manhole Required"
            checked={!!form.civilWorks.newManholeRequired}
            onToggle={() => toggle('civilWorks', 'newManholeRequired')}
            disabled={!editable}
          />
        </>
      );
    }

    if (step === 6) {
      const materials = form.materials || defaultMaterials;
      const civils = Array.isArray(materials.civils) ? materials.civils : [];
      const nte = Array.isArray(materials.nte) ? materials.nte : [];

      const Table = ({ title, kind, rows }) => (
        <View style={{ marginBottom: theme.spacing.xl }}>
          <View style={styles.tableHeaderRow}>
            <SectionTitle>{title}</SectionTitle>
            {!readOnly ? (
              <TouchableOpacity style={styles.addRowBtn} onPress={() => addMaterialRow(kind)} disabled={!editable}>
                <Feather name="plus" size={16} color={theme.colors.primary} />
                <Text style={styles.addRowText}>Add Row</Text>
              </TouchableOpacity>
            ) : null}
          </View>
          {rows.map((r, idx) => (
            <View key={`${kind}-${idx}`} style={styles.rowCard}>
              <Field
                label="Description"
                value={r.description}
                onChangeText={(v) => updateMaterialRow(kind, idx, 'description', v)}
                placeholder="Description"
                editable={editable}
              />
              <View style={styles.rowTwoCol}>
                <View style={{ flex: 1 }}>
                  <Field
                    label="Unit"
                    value={r.unit}
                    onChangeText={(v) => updateMaterialRow(kind, idx, 'unit', v)}
                    placeholder="Unit"
                    editable={editable}
                  />
                </View>
                <View style={{ flex: 1 }}>
                  <Field
                    label="Qty"
                    value={String(r.qty ?? '')}
                    onChangeText={(v) => updateMaterialRow(kind, idx, 'qty', v)}
                    placeholder="Qty"
                    keyboardType="numeric"
                    editable={editable}
                  />
                </View>
              </View>
              {!readOnly ? (
                <TouchableOpacity style={styles.removeRowBtn} onPress={() => removeMaterialRow(kind, idx)} disabled={!editable}>
                  <Feather name="trash-2" size={16} color={theme.colors.danger} />
                  <Text style={styles.removeRowText}>Remove</Text>
                </TouchableOpacity>
              ) : null}
            </View>
          ))}
        </View>
      );

      return (
        <>
          <Table title="Civils" kind="civils" rows={civils} />
          <Table title="NTE" kind="nte" rows={nte} />
        </>
      );
    }

    if (step === 7) {
      return (
        <>
          <SectionTitle>Notes</SectionTitle>
          <Field
            label="Notes (optional)"
            value={form?.notes?.notes || ''}
            onChangeText={(v) => setForm((prev) => ({ ...prev, notes: { ...(prev.notes || {}), notes: v } }))}
            placeholder="Anything to add..."
            multiline
            editable={editable}
          />
        </>
      );
    }

    if (step === 8) {
      return (
        <>
          <PhotoCard labelKey="nearest_joint_box" title="Nearest Joint Box" />
          <PhotoCard labelKey="fibre_route_towards_tower" title="Fibre Route towards Tower" />
          <PhotoCard labelKey="tower_overview" title="Tower Overview" />
          <PhotoCard labelKey="new_plinth_space" title="New Plinth Space" />
          <PhotoCard labelKey="power_connection_image" title="Power Connection Image" />
          <PhotoCard labelKey="termination_point_image" title="Termination Point Image" />
          <PhotoCard labelKey="route_sketch" title="Route Sketch with Measurements" />
        </>
      );
    }

    if (step === 9) {
      const selectedPhotos = Object.values(form.photos || {}).reduce((sum, v) => sum + (Array.isArray(v) ? v.length : (v?.uri ? 1 : 0)), 0);
      return (
        <>
          <SectionTitle>Summary</SectionTitle>
          <View style={styles.summaryCard}>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>Site Name: </Text>{form.general.siteName || '-'}</Text>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>JC Number: </Text>{form.general.jcNumber || '-'}</Text>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>Coordinates: </Text>{form.general.coordinates || '-'}</Text>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>Latitude: </Text>{form.general.latitude || '-'}</Text>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>Longitude: </Text>{form.general.longitude || '-'}</Text>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>Province/Region: </Text>{form.general.provinceRegion || '-'}</Text>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>Notes: </Text>{(form.notes?.notes || '').trim() ? 'Provided' : '—'}</Text>
            <Text style={styles.summaryLine}><Text style={styles.summaryKey}>Photos Selected: </Text>{selectedPhotos}</Text>
          </View>

          {!readOnly ? (
            <>
              <TouchableOpacity style={[styles.submitBtn, loading && styles.disabledBtn]} onPress={() => submit('draft')} disabled={loading}>
                {loading ? <ActivityIndicator color={theme.colors.white} /> : <Text style={styles.submitBtnText}>Save Draft</Text>}
              </TouchableOpacity>
              <TouchableOpacity style={[styles.submitBtnPrimary, loading && styles.disabledBtn]} onPress={() => submit('submitted')} disabled={loading}>
                {loading ? <ActivityIndicator color={theme.colors.white} /> : <Text style={styles.submitBtnText}>{isEdit ? 'Update & Submit' : 'Submit Survey'}</Text>}
              </TouchableOpacity>
              {mode === 'create' ? (
                <TouchableOpacity style={styles.resetBtn} onPress={() => Alert.alert('Reset draft', 'Clear all form values?', [
                  { text: 'Cancel', style: 'cancel' },
                  { text: 'Clear', style: 'destructive', onPress: clearDraft },
                ])} disabled={loading}>
                  <Text style={styles.resetBtnText}>Clear Draft</Text>
                </TouchableOpacity>
              ) : null}
            </>
          ) : null}
        </>
      );
    }

    return null;
  };

  const canGoBack = step > 0;
  const canGoNext = step < totalSteps - 1;

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : 'height'} style={{ flex: 1 }}>
        <View style={styles.progressWrap}>
          <Text style={styles.stepTitle}>{currentTitle}</Text>
          <Text style={styles.stepMeta}>Step {step + 1} of {totalSteps}</Text>
          <View style={styles.progressTrack}>
            <View style={[styles.progressFill, { width: `${Math.round(progress * 100)}%` }]} />
          </View>
        </View>

        <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
          {renderStep()}
        </ScrollView>

        <View style={[styles.navRow, { paddingBottom: Math.max(insets.bottom, theme.spacing.md) }]}>
          <TouchableOpacity
            style={[styles.navBtn, styles.navBtnSecondary, !canGoBack && styles.navBtnDisabled]}
            onPress={() => canGoBack && setStep((s) => Math.max(0, s - 1))}
            disabled={!canGoBack}
          >
            <Feather name="chevron-left" size={18} color={canGoBack ? theme.colors.text : theme.colors.muted} />
            <Text style={[styles.navBtnText, !canGoBack && styles.navBtnTextDisabled]}>Back</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.navBtn, styles.navBtnPrimary, !canGoNext && styles.navBtnDisabled]}
            onPress={() => canGoNext && setStep((s) => Math.min(totalSteps - 1, s + 1))}
            disabled={!canGoNext}
          >
            <Text style={[styles.navBtnTextPrimary, !canGoNext && styles.navBtnTextDisabled]}>Next</Text>
            <Feather name="chevron-right" size={18} color={canGoNext ? theme.colors.white : theme.colors.muted} />
          </TouchableOpacity>
        </View>

        <Modal visible={usersOpen} transparent animationType="fade" onRequestClose={() => setUsersOpen(false)}>
          <View style={styles.modalBackdrop}>
            <View style={styles.modalCard}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitle}>Select user</Text>
                <TouchableOpacity onPress={() => setUsersOpen(false)}>
                  <Feather name="x" size={20} color={theme.colors.secondaryText} />
                </TouchableOpacity>
              </View>
              <ScrollView style={{ maxHeight: 380 }} keyboardShouldPersistTaps="handled">
                {enabledUsers.length === 0 ? (
                  <View style={styles.modalEmpty}>
                    <Text style={styles.modalEmptyText}>No users found</Text>
                  </View>
                ) : (
                  enabledUsers.map((u) => (
                    <TouchableOpacity
                      key={String(u.id)}
                      style={styles.userRow}
                      onPress={() => {
                        setForm((prev) => ({
                          ...prev,
                          meta: { ...(prev.meta || {}), surveyPerformedByUserId: String(u.id), surveyPerformedBy: String(u.name || '') },
                        }));
                        setUsersOpen(false);
                      }}
                    >
                      <Text style={styles.userName} numberOfLines={1}>{u.name}</Text>
                      {String(form?.meta?.surveyPerformedByUserId || '') === String(u.id) ? (
                        <Feather name="check" size={18} color={theme.colors.primary} />
                      ) : (
                        <Feather name="chevron-right" size={18} color={theme.colors.muted} />
                      )}
                    </TouchableOpacity>
                  ))
                )}
              </ScrollView>
            </View>
          </View>
        </Modal>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  progressWrap: {
    paddingHorizontal: theme.spacing.lg,
    paddingTop: theme.spacing.md,
    paddingBottom: theme.spacing.md,
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.border,
    backgroundColor: theme.colors.background,
  },
  stepTitle: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.text },
  stepMeta: { marginTop: 2, fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText },
  progressTrack: {
    marginTop: theme.spacing.sm,
    height: 8,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.circle,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  progressFill: { height: '100%', backgroundColor: theme.colors.primary },
  scroll: { padding: theme.spacing.lg, paddingBottom: theme.spacing.xxl },
  sectionTitle: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.text, marginBottom: theme.spacing.md },
  label: { color: theme.colors.secondaryText, marginBottom: 6, fontSize: theme.fontSizes.sm, fontWeight: '600' },
  input: {
    backgroundColor: theme.colors.input,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    paddingHorizontal: 12,
    paddingVertical: 10,
    color: theme.colors.text,
  },
  selectInput: {
    backgroundColor: theme.colors.input,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    paddingHorizontal: 12,
    paddingVertical: 12,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: 10,
  },
  selectText: { color: theme.colors.text, fontWeight: '600', flex: 1 },
  textarea: { minHeight: 100, paddingTop: 10 },
  inputDisabled: { opacity: 0.75 },
  pillsRow: { flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  pill: {
    paddingVertical: 8,
    paddingHorizontal: 12,
    borderRadius: theme.borderRadius.circle,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.surface,
  },
  pillSelected: { borderColor: theme.colors.primary, backgroundColor: 'rgba(10, 126, 164, 0.16)' },
  pillDisabled: { opacity: 0.7 },
  pillText: { color: theme.colors.text, fontWeight: '600', fontSize: theme.fontSizes.sm },
  pillTextSelected: { color: theme.colors.primary },
  checkRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    backgroundColor: theme.colors.surface,
    marginBottom: theme.spacing.sm,
    gap: 10,
  },
  checkbox: {
    width: 20,
    height: 20,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: theme.colors.border,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: theme.colors.input,
  },
  checkboxChecked: { backgroundColor: theme.colors.primary, borderColor: theme.colors.primary },
  checkLabel: { color: theme.colors.text, fontWeight: '600', flex: 1 },
  tableHeaderRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: theme.spacing.sm },
  addRowBtn: { flexDirection: 'row', alignItems: 'center', gap: 6, paddingVertical: 8, paddingHorizontal: 10, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.surface, borderWidth: 1, borderColor: theme.colors.border },
  addRowText: { color: theme.colors.primary, fontWeight: '700' },
  rowCard: { backgroundColor: theme.colors.surface, borderRadius: theme.borderRadius.md, borderWidth: 1, borderColor: theme.colors.border, padding: theme.spacing.md, marginBottom: theme.spacing.md },
  rowTwoCol: { flexDirection: 'row', gap: 12 },
  removeRowBtn: { marginTop: 4, flexDirection: 'row', alignItems: 'center', gap: 8, alignSelf: 'flex-start' },
  removeRowText: { color: theme.colors.danger, fontWeight: '700' },
  photoCard: { backgroundColor: theme.colors.surface, borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, marginBottom: theme.spacing.md },
  photoTitle: { color: theme.colors.text, fontWeight: '700', marginBottom: theme.spacing.sm },
  photoStrip: { gap: 10 },
  photoThumbWrap: { width: 220, height: 180 },
  photoThumb: { width: '100%', height: '100%', borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.input },
  fileThumbWrap: {
    width: 220,
    height: 180,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.input,
    padding: 12,
    alignItems: 'center',
    justifyContent: 'center',
    gap: 10,
  },
  fileThumbText: { color: theme.colors.secondaryText, fontWeight: '700', fontSize: theme.fontSizes.sm, textAlign: 'center' },
  photoRemove: { position: 'absolute', top: 8, right: 8, width: 26, height: 26, borderRadius: 13, backgroundColor: theme.colors.danger, alignItems: 'center', justifyContent: 'center' },
  photoEmpty: { height: 180, borderRadius: theme.borderRadius.md, borderWidth: 1, borderColor: theme.colors.border, backgroundColor: theme.colors.input, alignItems: 'center', justifyContent: 'center', gap: 8 },
  photoEmptyText: { color: theme.colors.secondaryText, fontWeight: '600' },
  photoActions: { flexDirection: 'row', flexWrap: 'wrap', gap: 10, marginTop: theme.spacing.sm },
  photoBtn: { flexDirection: 'row', alignItems: 'center', gap: 8, paddingVertical: 10, paddingHorizontal: 12, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.background, borderWidth: 1, borderColor: theme.colors.border },
  photoBtnText: { color: theme.colors.text, fontWeight: '700' },
  photoBtnDanger: { flexDirection: 'row', alignItems: 'center', gap: 8, paddingVertical: 10, paddingHorizontal: 12, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.danger, borderWidth: 1, borderColor: theme.colors.danger },
  photoBtnDangerText: { color: theme.colors.white, fontWeight: '700' },
  summaryCard: { backgroundColor: theme.colors.surface, borderRadius: theme.borderRadius.md, borderWidth: 1, borderColor: theme.colors.border, padding: theme.spacing.md, marginBottom: theme.spacing.lg },
  summaryLine: { color: theme.colors.text, marginBottom: 8 },
  summaryKey: { color: theme.colors.secondaryText, fontWeight: '700' },
  submitBtn: { backgroundColor: theme.colors.warning, borderRadius: theme.borderRadius.md, paddingVertical: 14, alignItems: 'center', marginBottom: theme.spacing.md },
  submitBtnPrimary: { backgroundColor: theme.colors.primary, borderRadius: theme.borderRadius.md, paddingVertical: 14, alignItems: 'center', marginBottom: theme.spacing.md },
  submitBtnText: { color: theme.colors.white, fontWeight: '800' },
  resetBtn: { paddingVertical: 12, alignItems: 'center' },
  resetBtnText: { color: theme.colors.danger, fontWeight: '800' },
  disabledBtn: { opacity: 0.7 },
  navRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.md,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
    backgroundColor: theme.colors.background,
    gap: 12,
  },
  navBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    borderWidth: 1,
    borderColor: theme.colors.border,
    paddingHorizontal: 12,
    paddingVertical: 12,
    borderRadius: theme.borderRadius.md,
    flexBasis: '48%',
    flexGrow: 0,
    flexShrink: 1,
    justifyContent: 'center',
  },
  navBtnSecondary: { backgroundColor: theme.colors.surface },
  navBtnPrimary: { backgroundColor: theme.colors.primary, borderColor: theme.colors.primary },
  navBtnDisabled: { opacity: 0.6 },
  navBtnText: { color: theme.colors.text, fontWeight: '800' },
  navBtnTextPrimary: { color: theme.colors.white, fontWeight: '800' },
  navBtnTextDisabled: { color: theme.colors.muted },
  center: { paddingVertical: 40, alignItems: 'center', justifyContent: 'center', gap: 12 },
  centerText: { color: theme.colors.secondaryText, fontWeight: '600' },
  modalBackdrop: { flex: 1, backgroundColor: 'rgba(15,23,42,0.55)', alignItems: 'center', justifyContent: 'center', padding: theme.spacing.lg },
  modalCard: { width: '100%', backgroundColor: theme.colors.surface, borderRadius: theme.borderRadius.lg, borderWidth: 1, borderColor: theme.colors.border, padding: theme.spacing.md },
  modalHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: theme.spacing.sm },
  modalTitle: { fontSize: theme.fontSizes.md, fontWeight: '800', color: theme.colors.text },
  modalEmpty: { paddingVertical: 20, alignItems: 'center' },
  modalEmptyText: { color: theme.colors.secondaryText, fontWeight: '700' },
  userRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: 12, borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: theme.colors.border },
  userName: { flex: 1, color: theme.colors.text, fontWeight: '800', marginRight: 12 },
});
