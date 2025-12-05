import React, { useState } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ActivityIndicator, Alert, Image, ScrollView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { addFaultRemark } from '../services/api';
import { theme } from '../styles/theme';
import * as ImagePicker from 'expo-image-picker';

export default function AddRemarkScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { id } = route.params || {};

  const [remark, setRemark] = useState('');
  const [loading, setLoading] = useState(false);
  const [images, setImages] = useState([]);

  const pickImages = async () => {
    try {
      console.log('pickImages: start', { platform: Platform.OS });
      const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
      console.log('pickImages: permission', perm);
    if (!perm.granted) {
      Alert.alert('Permission required', 'Allow media library access to attach images.', [
        { text: 'Open Settings', onPress: () => { try { require('expo-linking').openSettings(); } catch (_) {} } },
        { text: 'Cancel', style: 'cancel' }
      ]);
      return;
    }
    const opts = Platform.OS === 'android'
      ? { mediaTypes: ['images'], quality: 0.8 }
      : { mediaTypes: ['images'], quality: 0.8 };
      console.log('pickImages: launch options', opts);
      const result = await ImagePicker.launchImageLibraryAsync(opts);
      console.log('pickImages: result', result);
    if (result.canceled) { Alert.alert('No image selected'); return; }
    const selected = (result.assets || []).map(a => ({ uri: a.uri, name: a.fileName || `attachment-${Date.now()}.jpg`, type: a.mimeType || 'image/jpeg' }));
    setImages(prev => [...prev, ...selected]);
    Alert.alert('Attached', `${selected.length} image(s) added`);
    } catch (e) {
      console.error('pickImages: error', e);
      Alert.alert('Error', 'Failed to open gallery.');
    }
  };

  const submit = async () => {
    if (!remark.trim()) {
      Alert.alert('Validation', 'Please enter a remark.');
      return;
    }
    setLoading(true);
    try {
      console.log('submitRemark: start', { imagesCount: images.length });
      if (images.length > 0) {
        const fd = new FormData();
        fd.append('remark', remark.trim());
        for (const img of images) {
          if (Platform.OS === 'web') {
            const blob = await (await fetch(img.uri)).blob();
            fd.append('attachments[]', blob, img.name);
          } else {
            fd.append('attachments[]', { uri: img.uri, name: img.name, type: img.type });
          }
        }
        const resp = await addFaultRemark(id, fd);
        console.log('submitRemark: response (form)', resp);
      } else {
        const resp = await addFaultRemark(id, { remark: remark.trim() });
        console.log('submitRemark: response (json)', resp);
      }
      Alert.alert('Success', 'Remark added successfully.', [
        { text: 'OK', onPress: () => navigation.replace('FaultDetail', { id, refetchAt: Date.now() }) }
      ]);
    } catch (e) {
      console.error('submitRemark: error', e);
      Alert.alert('Error', 'Failed to add remark. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <View style={styles.section}>
        <Text style={styles.title}>Add Remark</Text>
        <Text style={styles.subtitle}>Fault ID: {id}</Text>

        <Text style={styles.label}>Remark</Text>
        <TextInput
          style={styles.inputMultiline}
          placeholder="Type your remark here"
          value={remark}
          onChangeText={setRemark}
          multiline
          numberOfLines={5}
          textAlignVertical="top"
          placeholderTextColor={theme.colors.gray}
        />


        <TouchableOpacity style={styles.primaryBtn} onPress={submit} disabled={loading}>
          {loading ? (
            <ActivityIndicator color={theme.colors.white} />
          ) : (
            <Text style={styles.primaryBtnText}>Submit Remark</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity style={styles.secondaryBtn} onPress={pickImages} disabled={loading}>
          <Text style={styles.secondaryBtnText}>Attach Images</Text>
        </TouchableOpacity>

        <TouchableOpacity style={styles.secondaryBtn} onPress={async () => {
          console.log('capturePhoto: start', { platform: Platform.OS });
          const perm = await ImagePicker.requestCameraPermissionsAsync();
          console.log('capturePhoto: permission', perm);
          if (!perm.granted) {
            Alert.alert('Permission required', 'Allow camera access to capture images.', [
              { text: 'Open Settings', onPress: () => { try { require('expo-linking').openSettings(); } catch (_) {} } },
              { text: 'Cancel', style: 'cancel' }
            ]);
            return;
          }
          const opts = { mediaTypes: ['images'], quality: 0.8, cameraType: 'back' };
          console.log('capturePhoto: launch options', opts);
          const result = await ImagePicker.launchCameraAsync(opts);
          console.log('capturePhoto: result', result);
          if (result.canceled) { Alert.alert('Capture canceled'); return; }
          const a = result.assets?.[0];
          if (a) {
            setImages(prev => [...prev, { uri: a.uri, name: a.fileName || `capture-${Date.now()}.jpg`, type: a.mimeType || 'image/jpeg' }]);
            Alert.alert('Attached', 'Photo added');
          }
        }} disabled={loading}>
          <Text style={styles.secondaryBtnText}>Capture Photo</Text>
        </TouchableOpacity>

        {images.length > 0 && (
          <ScrollView horizontal showsHorizontalScrollIndicator={false} style={{ marginTop: theme.spacing.md }}>
            {images.map((img, i) => (
              <Image key={`${img.uri}-${i}`} source={{ uri: img.uri }} style={{ width: 100, height: 100, borderRadius: 8, marginRight: 8, backgroundColor: theme.colors.lightGray }} />
            ))}
          </ScrollView>
        )}

        <TouchableOpacity style={styles.secondaryBtn} onPress={() => navigation.goBack()} disabled={loading}>
          <Text style={styles.secondaryBtnText}>Cancel</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background, padding: theme.spacing.lg },
  section: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.md, padding: theme.spacing.lg, elevation: 1 },
  title: { fontSize: theme.fontSizes.xl, fontWeight: '700', color: theme.colors.dark, marginBottom: theme.spacing.sm },
  subtitle: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.lg },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.xs },
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingHorizontal: theme.spacing.md, paddingVertical: theme.spacing.sm, marginBottom: theme.spacing.lg, fontSize: theme.fontSizes.md, color: theme.colors.dark },
  inputMultiline: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingHorizontal: theme.spacing.md, paddingVertical: theme.spacing.md, marginBottom: theme.spacing.lg, fontSize: theme.fontSizes.md, minHeight: 120, color: theme.colors.text, backgroundColor: theme.colors.input },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center' },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' },
  secondaryBtn: { backgroundColor: theme.colors.white, borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.md },
  secondaryBtnText: { color: theme.colors.dark, fontSize: theme.fontSizes.md, fontWeight: '600' }
});
