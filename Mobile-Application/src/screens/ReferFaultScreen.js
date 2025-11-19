import React, { useEffect, useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { getSections, referFault } from '../services/api';
import { theme } from '../styles/theme';

export default function ReferFaultScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { id } = route.params || {};

  const [sections, setSections] = useState([]);
  const [selectedSection, setSelectedSection] = useState(null);
  const [showSectionList, setShowSectionList] = useState(false);
  const [notes, setNotes] = useState('');
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const load = async () => {
      try {
        const list = await getSections();
        setSections(Array.isArray(list) ? list : []);
      } catch (e) {}
    };
    load();
  }, []);

  const submit = async () => {
    if (!selectedSection || !notes.trim()) return;
    setLoading(true);
    try {
      const res = await referFault(id, { section_id: selectedSection.id, remark: notes });
      setResult(res);
      if (res?.success) {
        Alert.alert('Success', 'Fault referred successfully.', [
          { text: 'OK', onPress: () => navigation.navigate('FaultDetail', { id, refetchAt: Date.now() }) }
        ]);
      } else {
        Alert.alert('Error', res?.error || 'Failed to refer fault');
      }
    } catch (e) {
      setResult({ error: 'Failed to refer fault.' });
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} keyboardShouldPersistTaps="handled">
        <Text style={styles.title}>Refer Fault #{id}</Text>

        <Text style={styles.label}>Section</Text>
        <TouchableOpacity style={styles.select} onPress={() => setShowSectionList(v => !v)}>
          <Text style={styles.selectText}>{selectedSection ? selectedSection.section : 'Select section'}</Text>
        </TouchableOpacity>
        {showSectionList && (
          <View style={styles.dropdown}>
            {sections.map(s => (
              <TouchableOpacity key={s.id} style={styles.dropdownItem} onPress={() => { setSelectedSection(s); setShowSectionList(false); }}>
                <Text style={styles.dropdownItemText}>{s.section}</Text>
              </TouchableOpacity>
            ))}
          </View>
        )}

        <Text style={styles.label}>Work To Be Done</Text>
        <TextInput
          placeholder="Describe the work required"
          style={styles.input}
          multiline
          numberOfLines={4}
          value={notes}
          onChangeText={setNotes}
          placeholderTextColor={theme.colors.gray}
        />
        {result?.error ? <Text style={styles.error}>{result.error}</Text> : null}
        <TouchableOpacity style={[styles.primaryBtn, (!selectedSection || !notes.trim()) && { opacity: 0.6 }]} onPress={submit} disabled={loading || !selectedSection || !notes.trim()}>
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
  select: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, paddingHorizontal: theme.spacing.md, marginBottom: theme.spacing.md },
  selectText: { fontSize: theme.fontSizes.md, color: theme.colors.dark },
  dropdown: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, marginBottom: theme.spacing.md, maxHeight: 220 },
  dropdownItem: { paddingVertical: theme.spacing.md, paddingHorizontal: theme.spacing.md, borderBottomWidth: 1, borderBottomColor: theme.colors.lightGray },
  dropdownItemText: { fontSize: theme.fontSizes.md, color: theme.colors.dark },
  input: { backgroundColor: theme.colors.white, borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, padding: theme.spacing.md, minHeight: 120, textAlignVertical: 'top' },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.lg },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' },
  error: { color: 'red', marginTop: theme.spacing.sm }
});