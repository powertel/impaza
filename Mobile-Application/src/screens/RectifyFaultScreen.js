import React, { useEffect, useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { rectifyFault, getRFOs } from '../services/api';
import { theme } from '../styles/theme';

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

  const submit = async () => {
    setLoading(true);
    try {
      const res = await rectifyFault(id, { notes, confirmedRfo_id: selectedRfo?.id, activity: 'ON RECTIFICATION' });
      setResult(res);
      Alert.alert('Success', 'Fault rectified successfully.', [
        { text: 'OK', onPress: () => navigation.navigate('FaultDetail', { id, refetchAt: Date.now() }) }
      ]);
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
        <Text style={styles.label}>Confirmed Reason For Outage</Text>
        <TouchableOpacity style={styles.select} onPress={() => setShowRfoList(v => !v)}>
          <Text style={styles.selectText}>{selectedRfo ? selectedRfo.RFO : 'Select RFO'}</Text>
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
        <TextInput
          placeholder="Enter rectification notes"
          style={styles.input}
          multiline
          numberOfLines={4}
          value={notes}
          onChangeText={setNotes}
          placeholderTextColor={theme.colors.gray}
        />
        {result?.error ? <Text style={styles.error}>{result.error}</Text> : null}
        <TouchableOpacity style={[styles.primaryBtn, (!selectedRfo || !notes.trim()) && { opacity: 0.6 }]} onPress={submit} disabled={loading || !selectedRfo || !notes.trim()}>
          <Text style={styles.primaryBtnText}>{loading ? 'Submitting…' : 'Submit'}</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.white, padding: theme.spacing.lg },
  title: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.black, marginBottom: theme.spacing.md },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.dark, marginBottom: 6 },
  select: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, paddingHorizontal: theme.spacing.md, backgroundColor: theme.colors.input, marginBottom: theme.spacing.md },
  selectText: { color: theme.colors.text },
  dropdown: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, backgroundColor: theme.colors.white, marginBottom: theme.spacing.md },
  dropdownItem: { paddingVertical: 10, paddingHorizontal: theme.spacing.md },
  dropdownItemText: { color: theme.colors.text },
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, padding: theme.spacing.md, minHeight: 100, textAlignVertical: 'top', color: theme.colors.text, backgroundColor: theme.colors.input },
  error: { color: theme.colors.danger, marginTop: theme.spacing.sm },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.lg },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' }
});