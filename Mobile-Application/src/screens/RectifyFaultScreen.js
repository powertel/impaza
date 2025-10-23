import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { rectifyFault } from '../services/api';
import { theme } from '../styles/theme';

export default function RectifyFaultScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { id } = route.params || {};

  const [notes, setNotes] = useState('');
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    setLoading(true);
    try {
      const res = await rectifyFault(id, { notes });
      setResult(res);
      navigation.goBack();
    } catch (e) {
      setResult({ error: 'Failed to submit rectification.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} keyboardShouldPersistTaps="handled">
        <Text style={styles.title}>Rectify Fault #{id}</Text>
        <TextInput
          placeholder="Enter rectification notes"
          style={styles.input}
          multiline
          numberOfLines={4}
          value={notes}
          onChangeText={setNotes}
        />
        {result?.error ? <Text style={styles.error}>{result.error}</Text> : null}
        <TouchableOpacity style={styles.primaryBtn} onPress={submit} disabled={loading}>
          <Text style={styles.primaryBtnText}>{loading ? 'Submitting…' : 'Submit'}</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.white, padding: theme.spacing.lg },
  title: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.black, marginBottom: theme.spacing.md },
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, padding: theme.spacing.md, minHeight: 100, textAlignVertical: 'top' },
  error: { color: theme.colors.danger, marginTop: theme.spacing.sm },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.lg },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' }
});