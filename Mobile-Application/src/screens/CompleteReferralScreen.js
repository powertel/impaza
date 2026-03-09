import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { completeReferral } from '../services/api';
import { theme } from '../styles/theme';

export default function CompleteReferralScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault } = route.params || {};
  
  const [remark, setRemark] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async () => {
    if (!remark.trim()) {
      Alert.alert('Error', 'Please provide a completion remark');
      return;
    }

    setSubmitting(true);
    try {
      await completeReferral(fault.id, {
        remark: remark
      });
      Alert.alert('Success', 'Referral work completed', [
        { text: 'OK', onPress: () => navigation.navigate('ReferredFaults') }
      ]);
    } catch (e) {
      Alert.alert('Error', e.message || 'Operation failed');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <View style={styles.header}>
          <Text style={styles.title}>Complete Referral</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Fault Reference</Text>
          <Text style={styles.value}>{fault?.fault_ref_number || fault?.id}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Customer</Text>
          <Text style={styles.value}>{fault?.customer}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Completion Remark</Text>
          <TextInput
            style={styles.input}
            placeholder="Describe the work done..."
            value={remark}
            onChangeText={setRemark}
            multiline
          />
        </View>

        <TouchableOpacity 
          style={[styles.submitBtn, submitting && styles.disabledBtn]} 
          onPress={handleSubmit}
          disabled={submitting}
        >
          {submitting ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>Complete & Return</Text>}
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  scroll: { padding: theme.spacing.lg },
  header: { marginBottom: theme.spacing.xl },
  title: { fontSize: theme.fontSizes.xl, fontWeight: '700', color: theme.colors.dark },
  section: { marginBottom: theme.spacing.xl },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.sm, fontWeight: '600' },
  value: { fontSize: theme.fontSizes.lg, color: theme.colors.dark, fontWeight: '500' },
  input: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.sm, padding: theme.spacing.md, height: 120, textAlignVertical: 'top', fontSize: theme.fontSizes.md },
  submitBtn: { backgroundColor: theme.colors.primary, padding: theme.spacing.lg, borderRadius: theme.spacing.sm, alignItems: 'center', marginTop: theme.spacing.lg },
  disabledBtn: { opacity: 0.7 },
  submitBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.lg, fontWeight: '600' },
});
