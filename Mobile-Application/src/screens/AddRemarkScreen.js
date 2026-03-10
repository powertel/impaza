import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ActivityIndicator, Alert, Image, ScrollView, Platform, KeyboardAvoidingView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { addFaultRemark } from '../services/api';
import { theme } from '../styles/theme';
import * as ImagePicker from 'expo-image-picker';
import { Feather } from '@expo/vector-icons';

export default function AddRemarkScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault } = route.params || {};
  const faultId = fault?.id || route.params?.id;

  const [remark, setRemark] = useState('');
  const [loading, setLoading] = useState(false);
  const [images, setImages] = useState([]);

  useEffect(() => {
    navigation.setOptions({
      headerTitle: 'Add Remark',
      headerStyle: {
        backgroundColor: theme.colors.background,
      },
      headerTintColor: theme.colors.text,
      headerShadowVisible: false,
    });
  }, []);

  const pickImages = async () => {
    try {
      const perm = await ImagePicker.requestMediaLibraryPermissionsAsync();
      if (!perm.granted) {
        Alert.alert('Permission required', 'Allow media library access to attach images.', [
          { text: 'Open Settings', onPress: () => { try { require('expo-linking').openSettings(); } catch (_) {} } },
          { text: 'Cancel', style: 'cancel' }
        ]);
        return;
      }
      
      const result = await ImagePicker.launchImageLibraryAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        quality: 0.8,
        allowsMultipleSelection: true,
      });

      if (!result.canceled) {
        const newImages = (result.assets || []).map(a => ({ 
          uri: a.uri, 
          name: a.fileName || `attachment-${Date.now()}.jpg`, 
          type: a.mimeType || 'image/jpeg' 
        }));
        setImages(prev => [...prev, ...newImages]);
      }
    } catch (e) {
      Alert.alert('Error', 'Failed to open gallery.');
    }
  };

  const captureImage = async () => {
    try {
      const perm = await ImagePicker.requestCameraPermissionsAsync();
      if (!perm.granted) {
        Alert.alert('Permission required', 'Allow camera access to capture images.', [
          { text: 'Open Settings', onPress: () => { try { require('expo-linking').openSettings(); } catch (_) {} } },
          { text: 'Cancel', style: 'cancel' }
        ]);
        return;
      }

      const result = await ImagePicker.launchCameraAsync({
        mediaTypes: ImagePicker.MediaTypeOptions.Images,
        quality: 0.8,
      });

      if (!result.canceled && result.assets?.[0]) {
        const a = result.assets[0];
        const newImage = { 
          uri: a.uri, 
          name: a.fileName || `capture-${Date.now()}.jpg`, 
          type: a.mimeType || 'image/jpeg' 
        };
        setImages(prev => [...prev, newImage]);
      }
    } catch (e) {
      Alert.alert('Error', 'Failed to capture photo.');
    }
  };

  const removeImage = (index) => {
    setImages(prev => prev.filter((_, i) => i !== index));
  };

  const submit = async () => {
    if (!remark.trim()) {
      Alert.alert('Validation', 'Please enter a remark.');
      return;
    }
    setLoading(true);
    try {
      const fd = new FormData();
      fd.append('remark', remark.trim());
      
      if (images.length > 0) {
        images.forEach((img, index) => {
          fd.append('attachments[]', {
            uri: img.uri,
            name: img.name,
            type: img.type,
          });
        });
      }

      // Note: If using a real API that expects JSON for simple remarks, adjust accordingly.
      // But typically FormData handles both text and files.
      await addFaultRemark(faultId, fd);
      
      Alert.alert('Success', 'Remark added successfully.', [
        { text: 'OK', onPress: () => navigation.goBack() }
      ]);
    } catch (e) {
      console.error('submitRemark: error', e);
      Alert.alert('Error', 'Failed to add remark. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <KeyboardAvoidingView 
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        style={{ flex: 1 }}
      >
        <ScrollView contentContainerStyle={styles.scroll}>
          <View style={styles.card}>
            <Text style={styles.label}>Fault Reference</Text>
            <Text style={styles.value}>{fault?.fault_ref_number || fault?.ref_number || faultId}</Text>
          </View>

          <View style={styles.inputContainer}>
            <Text style={styles.sectionTitle}>Remark</Text>
            <TextInput
              style={styles.input}
              placeholder="Type your remark here..."
              placeholderTextColor={theme.colors.muted}
              value={remark}
              onChangeText={setRemark}
              multiline
              numberOfLines={6}
              textAlignVertical="top"
            />
          </View>

          <View style={styles.attachmentsContainer}>
            <View style={styles.row}>
              <Text style={styles.sectionTitle}>Attachments</Text>
              <View style={styles.actionButtons}>
                <TouchableOpacity style={styles.iconBtn} onPress={pickImages}>
                  <Feather name="image" size={20} color={theme.colors.primary} />
                </TouchableOpacity>
                <TouchableOpacity style={styles.iconBtn} onPress={captureImage}>
                  <Feather name="camera" size={20} color={theme.colors.primary} />
                </TouchableOpacity>
              </View>
            </View>

            {images.length > 0 ? (
              <ScrollView horizontal showsHorizontalScrollIndicator={false} style={styles.imageList}>
                {images.map((img, i) => (
                  <View key={i} style={styles.imageWrapper}>
                    <Image source={{ uri: img.uri }} style={styles.imageThumbnail} />
                    <TouchableOpacity 
                      style={styles.removeBtn} 
                      onPress={() => removeImage(i)}
                    >
                      <Feather name="x" size={12} color="#fff" />
                    </TouchableOpacity>
                  </View>
                ))}
              </ScrollView>
            ) : (
              <View style={styles.emptyAttachments}>
                <Text style={styles.emptyText}>No images attached</Text>
              </View>
            )}
          </View>
        </ScrollView>

        <View style={styles.footer}>
          <TouchableOpacity 
            style={[styles.submitBtn, loading && styles.disabledBtn]} 
            onPress={submit} 
            disabled={loading}
          >
            {loading ? (
              <ActivityIndicator color={theme.colors.white} />
            ) : (
              <Text style={styles.submitBtnText}>Submit Remark</Text>
            )}
          </TouchableOpacity>
        </View>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { 
    flex: 1, 
    backgroundColor: theme.colors.background 
  },
  scroll: { 
    padding: theme.spacing.lg,
    paddingBottom: 100
  },
  card: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    marginBottom: theme.spacing.lg,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  label: { 
    fontSize: theme.fontSizes.xs, 
    color: theme.colors.secondaryText, 
    marginBottom: 4, 
    textTransform: 'uppercase',
    letterSpacing: 0.5,
  },
  value: { 
    fontSize: theme.fontSizes.md, 
    color: theme.colors.text, 
    fontWeight: '600' 
  },
  inputContainer: {
    marginBottom: theme.spacing.xl,
  },
  sectionTitle: {
    fontSize: theme.fontSizes.lg,
    fontWeight: '600',
    color: theme.colors.text,
    marginBottom: theme.spacing.md,
  },
  input: {
    backgroundColor: theme.colors.input,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    minHeight: 150,
    fontSize: theme.fontSizes.md,
    color: theme.colors.text,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  attachmentsContainer: {
    marginBottom: theme.spacing.xl,
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: theme.spacing.md,
  },
  actionButtons: {
    flexDirection: 'row',
  },
  iconBtn: {
    backgroundColor: 'rgba(10, 126, 164, 0.1)',
    padding: 8,
    borderRadius: theme.borderRadius.md,
    marginLeft: theme.spacing.md,
  },
  imageList: {
    flexDirection: 'row',
  },
  imageWrapper: {
    marginRight: theme.spacing.md,
    position: 'relative',
  },
  imageThumbnail: {
    width: 80,
    height: 80,
    borderRadius: theme.borderRadius.md,
    backgroundColor: theme.colors.input,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  removeBtn: {
    position: 'absolute',
    top: -5,
    right: -5,
    backgroundColor: theme.colors.danger,
    width: 20,
    height: 20,
    borderRadius: 10,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: theme.colors.surface,
  },
  emptyAttachments: {
    padding: theme.spacing.lg,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderStyle: 'dashed',
  },
  emptyText: {
    color: theme.colors.secondaryText,
    fontStyle: 'italic',
  },
  footer: {
    padding: theme.spacing.lg,
    backgroundColor: theme.colors.surface,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
  },
  submitBtn: {
    backgroundColor: theme.colors.primary,
    paddingVertical: theme.spacing.lg,
    borderRadius: theme.borderRadius.md,
    alignItems: 'center',
  },
  disabledBtn: {
    opacity: 0.6,
  },
  submitBtnText: {
    color: theme.colors.white,
    fontSize: theme.fontSizes.lg,
    fontWeight: '600',
  },
});
