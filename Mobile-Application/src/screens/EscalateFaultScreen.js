import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { escalateFault } from '../services/api';
import { theme } from '../styles/theme';

export default function EscalateFaultScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { id } = route.params || {};

  const [notes, setNotes] = useState('');
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    if (!notes.trim()) return;
    setLoading(true);
    try {
      const res = await escalateFault(id, { remark: notes });
      setResult(res);
      if (res?.success) {
        Alert.alert('Success', 'Fault escalated successfully.', [
          { text: 'OK', onPress: () => navigation.navigate('Escalations') }
        ]);
      } else {
        Alert.alert('Error', res?.error || 'Failed to escalate fault');
      }
    } catch (e) {
      setResult({ error: 'Failed to escalate fault.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} keyboardShouldPersistTaps="handled">
        <Text style={styles.title}>Escalate Fault #{id}</Text>

        <Text style={styles.label}>Remark</Text>
        <TextInput
          placeholder="Provide context for escalation"
          style={styles.input}
          multiline
          numberOfLines={4}
          value={notes}
          onChangeText={setNotes}
          placeholderTextColor={theme.colors.gray}
        />
        {result?.error ? <Text style={styles.error}>{result.error}</Text> : null}
        <TouchableOpacity style={[styles.primaryBtn, (!notes.trim()) && { opacity: 0.6 }]} onPress={submit} disabled={loading || !notes.trim()}>
          <Text style={styles.primaryBtnText}>{loading ? 'Submitting…' : 'Submit'}</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background, padding: theme.spacing.lg },
  title: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark, marginBottom: theme.spacing.lg },
  label: { fontSize: theme.fontSizes.md, color: theme.colors.gray, fontWeight: '500', marginBottom: theme.spacing.xs },
  input: { backgroundColor: theme.colors.white, borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, padding: theme.spacing.md, minHeight: 120, textAlignVertical: 'top' },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.lg },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' },
  error: { color: 'red', marginTop: theme.spacing.sm }
})
