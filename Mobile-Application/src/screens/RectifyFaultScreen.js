import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Image, Alert, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import * as ImagePicker from 'expo-image-picker';
import { getRFOs, rectifyFault } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function RectifyFaultScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { id } = route.params || {};

  const [notes, setNotes] = useState('');
  const [rfos, setRfos] = useState([]);
  const [selectedRfo, setSelectedRfo] = useState(null);
  const [showRfoList, setShowRfoList] = useState(false);
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(false);
  const [images, setImages] = useState([]);

  useEffect(() => {
    const load = async () => {
      try {
        const list = await getRFOs();
        setRfos(Array.isArray(list) ? list : []);
      } catch (e) {
        // ignore
      }
    };
    load();
  }, []);

  const pickImages = async () => {
    const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!perm.granted) {
      Alert.alert('Permission required', 'Allow media library access to attach images.');
      return;
    }
    const opts = { mediaTypes: ImagePicker.MediaTypeOptions.Images, quality: 0.8 };
    const result = await ImagePicker.launchImageLibraryAsync(opts);
    if (result.canceled) return;
    const selected = (result.assets || []).map(a => ({ uri: a.uri, name: a.fileName || `attachment-${Date.now()}.jpg`, type: a.mimeType || 'image/jpeg' }));
    setImages(prev => [...prev, ...selected]);
  };

  const capturePhoto = async () => {
    try {
      const perm = await ImagePicker.requestCameraPermissionsAsync();
      if (!perm.granted) {
        Alert.alert('Permission required', 'Allow camera access to capture images.');
        return;
      }
      const result = await ImagePicker.launchCameraAsync({ mediaTypes: ImagePicker.MediaTypeOptions.Images, quality: 0.8 });
      if (result.canceled) return;
      const a = result.assets?.[0];
      if (a) {
        setImages(prev => [...prev, { uri: a.uri, name: a.fileName || `capture-${Date.now()}.jpg`, type: a.mimeType || 'image/jpeg' }]);
      }
    } catch (e) {
      Alert.alert('Error', 'Failed to open camera.');
    }
  };

  const submit = async () => {
    setLoading(true);
    try {
      let res;
      if (images.length > 0) {
        const fd = new FormData();
        fd.append('notes', notes);
        fd.append('confirmedRfo_id', String(selectedRfo?.id || ''));
        fd.append('activity', 'ON RECTIFICATION');
        for (const img of images) {
          if (Platform.OS === 'web') {
            const blob = await (await fetch(img.uri)).blob();
            fd.append('attachments[]', blob, img.name);
          } else {
            fd.append('attachments[]', { uri: img.uri, name: img.name, type: img.type });
          }
        }
        res = await rectifyFault(id, fd);
      } else {
        res = await rectifyFault(id, { notes, confirmedRfo_id: selectedRfo?.id, activity: 'ON RECTIFICATION' });
      }
      setResult(res);
      Alert.alert('Success', 'Fault rectified successfully.', [
        { text: 'OK', onPress: () => navigation.navigate('RectifiedFaults') }
      ]);
    } catch (e) {
      setResult({ error: 'Failed to submit rectification.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <View style={styles.screen}>
      <SafeAreaView style={{ flex: 1 }} edges={["top","left","right"]}>
        <View style={styles.header}>
          <TouchableOpacity onPress={() => navigation.goBack()} style={styles.backBtn}>
            <Feather name="arrow-left" size={24} color={theme.colors.text} />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Rectify Fault</Text>
          <View style={{ width: 40 }} />
        </View>

        <ScrollView contentContainerStyle={{ padding: theme.spacing.lg }} keyboardShouldPersistTaps="handled">
          <View style={styles.formGroup}>
            <Text style={styles.label}>Confirmed RFO</Text>
            <TouchableOpacity style={styles.select} onPress={() => setShowRfoList(v => !v)}>
              <Text style={[styles.selectText, !selectedRfo && { color: theme.colors.muted }]}>
                {selectedRfo ? selectedRfo.RFO : 'Select Reason For Outage'}
              </Text>
              <Feather name="chevron-down" size={20} color={theme.colors.secondaryText} />
            </TouchableOpacity>
            {showRfoList && (
              <View style={styles.dropdown}>
                {rfos.map(r => (
                  <TouchableOpacity key={r.id} style={styles.dropdownItem} onPress={() => { setSelectedRfo(r); setShowRfoList(false); }}>
                    <Text style={styles.dropdownItemText}>{r.RFO}</Text>
                  </TouchableOpacity>
                ))}
              </View>
            )}
          </View>

          <View style={styles.formGroup}>
            <Text style={styles.label}>Rectification Notes</Text>
            <TextInput
              placeholder="Describe the work done..."
              style={styles.input}
              multiline
              numberOfLines={4}
              value={notes}
              onChangeText={setNotes}
              placeholderTextColor={theme.colors.muted}
            />
          </View>

          <View style={styles.mediaActions}>
            <TouchableOpacity style={styles.mediaBtn} onPress={pickImages}>
              <Feather name="image" size={20} color={theme.colors.primary} />
              <Text style={styles.mediaBtnText}>Attach Image</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.mediaBtn} onPress={capturePhoto}>
              <Feather name="camera" size={20} color={theme.colors.primary} />
              <Text style={styles.mediaBtnText}>Take Photo</Text>
            </TouchableOpacity>
          </View>

          {images.length > 0 && (
            <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.imagePreview}>
              {images.map((img, i) => (
                <View key={i} style={styles.imageWrapper}>
                  <Image source={{ uri: img.uri }} style={styles.previewImage} />
                  <TouchableOpacity 
                    style={styles.removeBtn} 
                    onPress={() => setImages(prev => prev.filter((_, idx) => idx !== i))}
                  >
                    <Feather name="x" size={12} color="white" />
                  </TouchableOpacity>
                </View>
              ))}
            </ScrollView>
          )}

          {result?.error && (
            <View style={styles.errorContainer}>
              <Feather name="alert-circle" size={16} color={theme.colors.danger} />
              <Text style={styles.errorText}>{result.error}</Text>
            </View>
          )}

          <TouchableOpacity 
            style={[styles.submitBtn, (!selectedRfo || !notes.trim()) && styles.disabledBtn]} 
            onPress={submit} 
            disabled={loading || !selectedRfo || !notes.trim()}
          >
            {loading ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>Submit Rectification</Text>}
          </TouchableOpacity>
        </ScrollView>
      </SafeAreaView>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  header: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingHorizontal: theme.spacing.lg, paddingVertical: theme.spacing.md },
  backBtn: { padding: 8, borderRadius: theme.borderRadius.circle, backgroundColor: theme.colors.surface },
  headerTitle: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.text },
  formGroup: { marginBottom: theme.spacing.lg },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText, marginBottom: 8, fontWeight: '500' },
  select: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, backgroundColor: theme.colors.input },
  selectText: { color: theme.colors.text, fontSize: theme.fontSizes.md },
  dropdown: { borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.surface, marginTop: 4 },
  dropdownItem: { padding: theme.spacing.md, borderBottomWidth: 1, borderBottomColor: theme.colors.border },
  dropdownItemText: { color: theme.colors.text },
  input: { borderWidth: 1, borderColor: theme.colors.border, borderRadius: theme.borderRadius.md, padding: theme.spacing.md, minHeight: 120, textAlignVertical: 'top', color: theme.colors.text, backgroundColor: theme.colors.input, fontSize: theme.fontSizes.md },
  mediaActions: { flexDirection: 'row', gap: theme.spacing.md, marginBottom: theme.spacing.lg },
  mediaBtn: { flex: 1, flexDirection: 'row', alignItems: 'center', justifyContent: 'center', padding: theme.spacing.md, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.surface, borderWidth: 1, borderColor: theme.colors.border },
  mediaBtnText: { marginLeft: 8, color: theme.colors.text, fontWeight: '600' },
  imagePreview: { marginBottom: theme.spacing.lg },
  imageWrapper: { position: 'relative', marginRight: 12 },
  previewImage: { width: 80, height: 80, borderRadius: theme.borderRadius.md, backgroundColor: theme.colors.surface },
  removeBtn: { position: 'absolute', top: -6, right: -6, backgroundColor: theme.colors.danger, borderRadius: 10, width: 20, height: 20, alignItems: 'center', justifyContent: 'center', borderWidth: 1, borderColor: theme.colors.background },
  errorContainer: { flexDirection: 'row', alignItems: 'center', backgroundColor: theme.colors.danger + '20', padding: 12, borderRadius: theme.borderRadius.md, marginBottom: theme.spacing.lg },
  errorText: { color: theme.colors.danger, marginLeft: 8, fontSize: theme.fontSizes.sm },
  submitBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.borderRadius.md, paddingVertical: 16, alignItems: 'center' },
  submitBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '700' },
  disabledBtn: { opacity: 0.6 },
});
