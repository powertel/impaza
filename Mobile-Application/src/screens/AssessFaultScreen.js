import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert, KeyboardAvoidingView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { assessFault } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function AssessFaultScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault } = route.params || {};

  // Priorities and Types options
  const PRIORITIES = [
    { label: 'High', value: 'High', color: theme.colors.danger },
    { label: 'Medium', value: 'Medium', color: theme.colors.warning },
    { label: 'Low', value: 'Low', color: theme.colors.success }
  ];
  
  const FAULT_TYPES = ['Physical', 'Logical'];

  const [priority, setPriority] = useState('Low');
  const [faultType, setFaultType] = useState('Physical');
  const [remark, setRemark] = useState('');
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    navigation.setOptions({
      headerTitle: 'Assess Fault',
      headerStyle: {
        backgroundColor: theme.colors.background,
      },
      headerTintColor: theme.colors.text,
      headerShadowVisible: false,
    });
  }, []);

  const handleSubmit = async () => {
    if (!remark.trim()) {
      Alert.alert('Error', 'Please provide an assessment remark');
      return;
    }

    setSubmitting(true);
    try {
      // API call with specific field names expected by backend
      await assessFault(fault.id, {
        priorityLevel: priority,
        faultType: faultType,
        remark: remark
      });
      
      Alert.alert('Success', 'Fault assessed successfully', [
        { text: 'OK', onPress: () => navigation.goBack() }
      ]);
    } catch (e) {
      Alert.alert('Error', e.message || 'Assessment failed');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <KeyboardAvoidingView 
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        style={{ flex: 1 }}
      >
        <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
          <View style={styles.card}>
            <Text style={styles.label}>Fault Reference</Text>
            <Text style={styles.value}>{fault?.fault_ref_number || fault?.ref_number || fault?.id}</Text>
            
            <View style={styles.divider} />
            
            <Text style={styles.label}>Customer</Text>
            <Text style={styles.value}>{fault?.customer_name || fault?.customer || 'N/A'}</Text>
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Priority Level</Text>
            <View style={styles.optionsContainer}>
              {PRIORITIES.map((p) => {
                const isSelected = priority === p.value;
                return (
                  <TouchableOpacity
                    key={p.value}
                    style={[
                      styles.optionBtn, 
                      isSelected && { backgroundColor: p.color, borderColor: p.color }
                    ]}
                    onPress={() => setPriority(p.value)}
                  >
                    <Text style={[
                      styles.optionText, 
                      isSelected && styles.selectedOptionText
                    ]}>{p.label}</Text>
                  </TouchableOpacity>
                );
              })}
            </View>
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Fault Type</Text>
            <View style={styles.optionsContainer}>
              {FAULT_TYPES.map((t) => {
                const isSelected = faultType === t;
                return (
                  <TouchableOpacity
                    key={t}
                    style={[
                      styles.optionBtn, 
                      isSelected && { backgroundColor: theme.colors.primary, borderColor: theme.colors.primary }
                    ]}
                    onPress={() => setFaultType(t)}
                  >
                    <Text style={[
                      styles.optionText, 
                      isSelected && styles.selectedOptionText
                    ]}>{t}</Text>
                  </TouchableOpacity>
                );
              })}
            </View>
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Assessment Remark</Text>
            <TextInput
              style={styles.input}
              placeholder="Enter your assessment details here..."
              placeholderTextColor={theme.colors.muted}
              value={remark}
              onChangeText={setRemark}
              multiline
              numberOfLines={5}
              textAlignVertical="top"
            />
          </View>

          <TouchableOpacity 
            style={[styles.submitBtn, submitting && styles.disabledBtn]} 
            onPress={handleSubmit}
            disabled={submitting}
          >
            {submitting ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.submitBtnText}>Submit Assessment</Text>
            )}
          </TouchableOpacity>
        </ScrollView>
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
    paddingBottom: theme.spacing.xxl
  },
  card: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    marginBottom: theme.spacing.xl,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  divider: {
    height: 1,
    backgroundColor: theme.colors.border,
    marginVertical: theme.spacing.md,
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
  section: { 
    marginBottom: theme.spacing.xl 
  },
  sectionTitle: {
    fontSize: theme.fontSizes.lg,
    fontWeight: '600',
    color: theme.colors.text,
    marginBottom: theme.spacing.md,
  },
  optionsContainer: {
    flexDirection: 'row',
    gap: theme.spacing.md,
  },
  optionBtn: {
    flex: 1,
    paddingVertical: theme.spacing.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.md,
    alignItems: 'center',
    backgroundColor: theme.colors.surface,
  },
  optionText: {
    color: theme.colors.text,
    fontWeight: '500',
    fontSize: theme.fontSizes.md,
  },
  selectedOptionText: {
    color: theme.colors.white,
    fontWeight: '700',
  },
  input: {
    backgroundColor: theme.colors.input,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    height: 150,
    fontSize: theme.fontSizes.md,
    color: theme.colors.text,
    borderWidth: 1,
    borderColor: theme.colors.border,
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
