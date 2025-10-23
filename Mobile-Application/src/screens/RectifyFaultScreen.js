import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { rectifyFault } from '../services/api';

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
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: '#fff', padding: 16 },
  title: { fontSize: 18, fontWeight: '700', color: '#111827', marginBottom: 12 },
  input: { borderWidth: 1, borderColor: '#D1D5DB', borderRadius: 8, padding: 12, minHeight: 100, textAlignVertical: 'top' },
  error: { color: '#DC2626', marginTop: 8 },
  primaryBtn: { backgroundColor: '#0A66CC', borderRadius: 8, paddingVertical: 14, alignItems: 'center', marginTop: 16 },
  primaryBtnText: { color: '#fff', fontSize: 16, fontWeight: '600' }
});