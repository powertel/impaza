import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { getAssignableTechnicians, assignFault, reassignFault } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function AssignFaultScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault, mode } = route.params || {}; // mode: 'assign' or 'reassign'
  
  const [technicians, setTechnicians] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedTech, setSelectedTech] = useState(mode === 'reassign' && fault?.assignedTo ? fault.assignedTo : null);
  const [remark, setRemark] = useState('');
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    loadTechnicians();
  }, []);

  const loadTechnicians = async () => {
    setLoading(true);
    try {
      const data = await getAssignableTechnicians();
      setTechnicians(Array.isArray(data) ? data : []);
    } catch (e) {
      Alert.alert('Error', 'Failed to load technicians');
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async () => {
    if (!selectedTech) {
      Alert.alert('Error', 'Please select a technician');
      return;
    }
    if (mode === 'reassign' && !remark.trim()) {
      Alert.alert('Error', 'Please provide a remark for reassignment');
      return;
    }

    setSubmitting(true);
    try {
      if (mode === 'assign') {
        await assignFault({
          fault_id: fault.id,
          assignedTo: selectedTech
        });
        Alert.alert('Success', 'Fault assigned successfully', [
          { text: 'OK', onPress: () => navigation.navigate('UnassignedFaults') }
        ]);
      } else {
        await reassignFault(fault.id, {
          assignedTo: selectedTech,
          remark: remark
        });
        Alert.alert('Success', 'Fault reassigned successfully', [
          { text: 'OK', onPress: () => navigation.navigate('SectionFaults') }
        ]);
      }
    } catch (e) {
      Alert.alert('Error', e.message || 'Operation failed');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <View style={styles.section}>
          <Text style={styles.label}>Fault Reference</Text>
          <Text style={styles.value}>{fault?.fault_ref_number || fault?.id}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Customer</Text>
          <Text style={styles.value}>{fault?.customer}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Select Technician</Text>
          {loading ? (
            <ActivityIndicator size="small" color={theme.colors.primary} />
          ) : (
            <View style={styles.techList}>
              {technicians.map(tech => (
                <TouchableOpacity
                  key={tech.id}
                  style={[styles.techItem, selectedTech === tech.id && styles.selectedTech]}
                  onPress={() => setSelectedTech(tech.id)}
                >
                  <Text style={[styles.techName, selectedTech === tech.id && styles.selectedTechText]}>{tech.name}</Text>
                  {selectedTech === tech.id && <Feather name="check" size={20} color={theme.colors.white} />}
                </TouchableOpacity>
              ))}
              {technicians.length === 0 && <Text style={styles.emptyText}>No assignable technicians found.</Text>}
            </View>
          )}
        </View>

        {mode === 'reassign' && (
          <View style={styles.section}>
            <Text style={styles.label}>Reason for Reassignment</Text>
            <TextInput
              style={styles.input}
              placeholder="Enter remark..."
              value={remark}
              onChangeText={setRemark}
              multiline
            />
          </View>
        )}

        <TouchableOpacity 
          style={[styles.submitBtn, submitting && styles.disabledBtn]} 
          onPress={handleSubmit}
          disabled={submitting}
        >
          {submitting ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>{mode === 'assign' ? 'Assign Fault' : 'Reassign Fault'}</Text>}
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  scroll: { padding: theme.spacing.lg },
  section: { marginBottom: theme.spacing.xl },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.sm, fontWeight: '600' },
  value: { fontSize: theme.fontSizes.lg, color: theme.colors.dark, fontWeight: '500' },
  techList: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.sm, overflow: 'hidden' },
  techItem: { padding: theme.spacing.md, borderBottomWidth: 1, borderBottomColor: theme.colors.lightGray, flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  selectedTech: { backgroundColor: theme.colors.primary },
  techName: { fontSize: theme.fontSizes.md, color: theme.colors.dark },
  selectedTechText: { color: theme.colors.white, fontWeight: '600' },
  emptyText: { padding: theme.spacing.md, textAlign: 'center', color: theme.colors.gray },
  input: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.sm, padding: theme.spacing.md, height: 100, textAlignVertical: 'top', fontSize: theme.fontSizes.md },
  submitBtn: { backgroundColor: theme.colors.primary, padding: theme.spacing.lg, borderRadius: theme.spacing.sm, alignItems: 'center', marginTop: theme.spacing.lg },
  disabledBtn: { opacity: 0.7 },
  submitBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.lg, fontWeight: '600' },
});
