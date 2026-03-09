import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { assessFault } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function AssessFaultScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault } = route.params || {};

  const [priority, setPriority] = useState('Low');
  const [faultType, setFaultType] = useState('Physical');
  const [remark, setRemark] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const priorities = ['High', 'Medium', 'Low'];
  const types = ['Physical', 'Logical'];

  const handleSubmit = async () => {
    if (!remark.trim()) {
      Alert.alert('Error', 'Please provide an assessment remark');
      return;
    }

    setSubmitting(true);
    try {
      await assessFault(fault.id, {
        priorityLevel: priority,
        faultType: faultType,
        remark: remark
      });
      Alert.alert('Success', 'Fault assessed successfully', [
        { text: 'OK', onPress: () => navigation.navigate('Assessments') }
      ]);
    } catch (e) {
      Alert.alert('Error', e.message || 'Assessment failed');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <ScrollView contentContainerStyle={styles.scroll}>
        <View style={styles.header}>
          <Text style={styles.title}>Assess Fault</Text>
          <Text style={styles.subtitle}>Ref: {fault?.fault_ref_number}</Text>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Priority Level</Text>
          <View style={styles.optionRow}>
            {priorities.map(p => (
              <TouchableOpacity
                key={p}
                style={[styles.optionBtn, priority === p && styles.selectedOption]}
                onPress={() => setPriority(p)}
              >
                <Text style={[styles.optionText, priority === p && styles.selectedOptionText]}>{p}</Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Fault Type</Text>
          <View style={styles.optionRow}>
            {types.map(t => (
              <TouchableOpacity
                key={t}
                style={[styles.optionBtn, faultType === t && styles.selectedOption]}
                onPress={() => setFaultType(t)}
              >
                <Text style={[styles.optionText, faultType === t && styles.selectedOptionText]}>{t}</Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        <View style={styles.section}>
          <Text style={styles.label}>Assessment Remark</Text>
          <TextInput
            style={styles.input}
            placeholder="Enter your assessment details here..."
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
          {submitting ? <ActivityIndicator color="#fff" /> : <Text style={styles.submitBtnText}>Submit Assessment</Text>}
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
  optionRow: { flexDirection: 'row', gap: theme.spacing.md },
  optionBtn: { flex: 1, paddingVertical: theme.spacing.md, borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, alignItems: 'center', backgroundColor: theme.colors.white },
  selectedOption: { backgroundColor: theme.colors.primary, borderColor: theme.colors.primary },
  optionText: { color: theme.colors.dark, fontWeight: '500' },
  selectedOptionText: { color: theme.colors.white, fontWeight: '700' },
  input: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.sm, padding: theme.spacing.md, height: 120, textAlignVertical: 'top', fontSize: theme.fontSizes.md, borderWidth: 1, borderColor: theme.colors.lightGray },
  submitBtn: { backgroundColor: theme.colors.primary, padding: theme.spacing.lg, borderRadius: theme.spacing.sm, alignItems: 'center', marginTop: theme.spacing.lg },
  disabledBtn: { opacity: 0.7 },
  submitBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.lg, fontWeight: '600' },
});
