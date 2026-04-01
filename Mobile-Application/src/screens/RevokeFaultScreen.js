import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { revokeFault } from '../services/api';
import { theme } from '../styles/theme';

export default function RevokeFaultScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault } = route.params || {};
  const [remark, setRemark] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async () => {
    if (!remark.trim()) {
      Alert.alert('Error', 'Please provide a reason for revoking this fault.');
      return;
    }

    setSubmitting(true);
    try {
      await revokeFault(fault.id, { remark });
      Alert.alert('Success', 'Fault revoked successfully.', [
        { text: 'OK', onPress: () => navigation.navigate('RectifiedFaults') }
      ]);
    } catch (e) {
      Alert.alert('Error', e.message || 'Failed to revoke fault.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <View style={styles.header}>
          <Text style={styles.title}>Revoke Fault</Text>
          <Text style={styles.subtitle}>Ref: {fault?.fault_ref_number}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Revocation Reason</Text>
          <TextInput
            style={styles.input}
            placeholder="Why is this fault being revoked?"
            value={remark}
            onChangeText={setRemark}
            multiline
            numberOfLines={4}
          />
        </View>

        <TouchableOpacity 
          style={[styles.submitBtn, submitting && styles.disabledBtn]} 
          onPress={handleSubmit}
          disabled={submitting}
        >
          {submitting ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>Revoke Fault</Text>}
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  scroll: { padding: theme.spacing.lg },
  header: { marginBottom: theme.spacing.xl },
  title: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark, marginBottom: 4 },
  subtitle: { fontSize: theme.fontSizes.md, color: theme.colors.gray },
  section: { marginBottom: theme.spacing.xl },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.sm, fontWeight: '600' },
  input: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.sm, padding: theme.spacing.md, height: 120, textAlignVertical: 'top', fontSize: theme.fontSizes.md, borderWidth: 1, borderColor: theme.colors.lightGray },
  submitBtn: { backgroundColor: theme.colors.danger, padding: theme.spacing.lg, borderRadius: theme.spacing.sm, alignItems: 'center', marginTop: theme.spacing.lg },
  disabledBtn: { opacity: 0.7 },
  submitBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.lg, fontWeight: '600' },
});
