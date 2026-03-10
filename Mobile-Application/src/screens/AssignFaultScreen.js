import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert, KeyboardAvoidingView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { getAssignableTechnicians, assignFault, reassignFault, reassignReferral } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function AssignFaultScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault, mode } = route.params || {}; // mode: 'assign', 'reassign', or 'referral-reassign'
  
  const [technicians, setTechnicians] = useState([]);
  const [loading, setLoading] = useState(false);
  const [selectedTech, setSelectedTech] = useState(mode === 'reassign' && fault?.assignedTo ? fault.assignedTo : null);
  const [remark, setRemark] = useState('');
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    loadTechnicians();
    
    // Set title based on mode
    let title = 'Assign Fault';
    if (mode === 'reassign') title = 'Reassign Fault';
    else if (mode === 'referral-reassign') title = 'Accept & Reassign';
    
    navigation.setOptions({
      headerTitle: title,
      headerStyle: {
        backgroundColor: theme.colors.background,
      },
      headerTintColor: theme.colors.text,
      headerShadowVisible: false,
    });
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
    if ((mode === 'reassign' || mode === 'referral-reassign') && !remark.trim()) {
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
          { text: 'OK', onPress: () => navigation.goBack() }
        ]);
      } else if (mode === 'reassign') {
        await reassignFault(fault.id, {
          assignedTo: selectedTech,
          remark: remark
        });
        Alert.alert('Success', 'Fault reassigned successfully', [
          { text: 'OK', onPress: () => navigation.goBack() }
        ]);
      } else if (mode === 'referral-reassign') {
        await reassignReferral(fault.id, {
          assignedTo: selectedTech,
          remark: remark
        });
        Alert.alert('Success', 'Referral accepted and reassigned successfully', [
          { text: 'OK', onPress: () => navigation.goBack() }
        ]);
      }
    } catch (e) {
      Alert.alert('Error', e.message || 'Operation failed');
    } finally {
      setSubmitting(false);
    }
  };

  const getButtonText = () => {
    switch (mode) {
      case 'assign': return 'Assign Fault';
      case 'reassign': return 'Reassign Fault';
      case 'referral-reassign': return 'Accept & Reassign';
      default: return 'Submit';
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
            <View style={styles.row}>
              <View>
                <Text style={styles.label}>Fault Reference</Text>
                <Text style={styles.value}>{fault?.fault_ref_number || fault?.ref_number || fault?.id}</Text>
              </View>
              <View style={{ alignItems: 'flex-end' }}>
                <Text style={styles.label}>Status</Text>
                <View style={styles.statusBadge}>
                  <Text style={styles.statusText}>{fault?.status || 'Unknown'}</Text>
                </View>
              </View>
            </View>
            
            <View style={styles.divider} />
            
            <View>
              <Text style={styles.label}>Customer</Text>
              <Text style={styles.value}>{fault?.customer_name || fault?.customer || 'N/A'}</Text>
            </View>
            
            {fault?.address && (
              <View style={{ marginTop: theme.spacing.md }}>
                <Text style={styles.label}>Address</Text>
                <Text style={styles.value} numberOfLines={2}>{fault.address}</Text>
              </View>
            )}
          </View>

          <Text style={styles.sectionTitle}>Select Technician</Text>
          
          {loading ? (
            <ActivityIndicator size="large" color={theme.colors.primary} style={{ marginVertical: 20 }} />
          ) : (
            <View style={styles.techList}>
              {technicians.length > 0 ? (
                technicians.map(tech => (
                  <TouchableOpacity
                    key={tech.id}
                    style={[
                      styles.techItem, 
                      selectedTech === tech.id && styles.selectedTechItem
                    ]}
                    onPress={() => setSelectedTech(tech.id)}
                  >
                    <View style={styles.techInfo}>
                      <View style={[
                        styles.avatar, 
                        selectedTech === tech.id && { backgroundColor: 'rgba(255,255,255,0.2)' }
                      ]}>
                        <Text style={[
                          styles.avatarText,
                          selectedTech === tech.id && { color: theme.colors.white }
                        ]}>
                          {tech.name?.charAt(0) || 'T'}
                        </Text>
                      </View>
                      <View>
                        <Text style={[
                          styles.techName, 
                          selectedTech === tech.id && styles.selectedTechText
                        ]}>{tech.name}</Text>
                        <Text style={[
                          styles.techRole,
                          selectedTech === tech.id && { color: 'rgba(255,255,255,0.7)' }
                        ]}>{tech.role || 'Technician'}</Text>
                      </View>
                    </View>
                    
                    <View style={[
                      styles.radioButton,
                      selectedTech === tech.id && styles.radioButtonSelected
                    ]}>
                      {selectedTech === tech.id && <View style={styles.radioButtonInner} />}
                    </View>
                  </TouchableOpacity>
                ))
              ) : (
                <View style={styles.emptyState}>
                  <Feather name="users" size={48} color={theme.colors.muted} />
                  <Text style={styles.emptyText}>No technicians available for assignment.</Text>
                </View>
              )}
            </View>
          )}

          {(mode === 'reassign' || mode === 'referral-reassign') && (
            <View style={styles.inputSection}>
              <Text style={styles.sectionTitle}>Reason for Reassignment</Text>
              <TextInput
                style={styles.input}
                placeholder="Enter reason for reassignment..."
                placeholderTextColor={theme.colors.muted}
                value={remark}
                onChangeText={setRemark}
                multiline
                numberOfLines={4}
              />
            </View>
          )}

          <TouchableOpacity 
            style={[styles.submitBtn, submitting && styles.disabledBtn]} 
            onPress={handleSubmit}
            disabled={submitting}
          >
            {submitting ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <Text style={styles.submitBtnText}>{getButtonText()}</Text>
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
    borderRadius: theme.borderRadius.lg,
    padding: theme.spacing.lg,
    marginBottom: theme.spacing.xl,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
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
  statusBadge: {
    paddingHorizontal: theme.spacing.sm,
    paddingVertical: 2,
    backgroundColor: 'rgba(59, 130, 246, 0.15)',
    borderRadius: theme.borderRadius.sm,
    borderWidth: 1,
    borderColor: 'rgba(59, 130, 246, 0.3)',
  },
  statusText: {
    color: '#60A5FA',
    fontSize: 12,
    fontWeight: '600',
  },
  sectionTitle: {
    fontSize: theme.fontSizes.lg,
    fontWeight: '600',
    color: theme.colors.text,
    marginBottom: theme.spacing.md,
    marginTop: theme.spacing.sm,
  },
  techList: {
    marginBottom: theme.spacing.xl,
  },
  techItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: theme.colors.surface,
    padding: theme.spacing.md,
    borderRadius: theme.borderRadius.md,
    marginBottom: theme.spacing.sm,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  selectedTechItem: {
    backgroundColor: theme.colors.primary,
    borderColor: theme.colors.primary,
  },
  techInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  avatar: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: theme.colors.input,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: theme.spacing.md,
  },
  avatarText: {
    fontSize: 18,
    fontWeight: '600',
    color: theme.colors.primary,
  },
  techName: {
    fontSize: theme.fontSizes.md,
    fontWeight: '600',
    color: theme.colors.text,
  },
  selectedTechText: {
    color: theme.colors.white,
  },
  techRole: {
    fontSize: theme.fontSizes.sm,
    color: theme.colors.secondaryText,
  },
  radioButton: {
    width: 20,
    height: 20,
    borderRadius: 10,
    borderWidth: 2,
    borderColor: theme.colors.secondaryText,
    alignItems: 'center',
    justifyContent: 'center',
  },
  radioButtonSelected: {
    borderColor: theme.colors.white,
    backgroundColor: 'rgba(255,255,255,0.2)',
  },
  radioButtonInner: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: theme.colors.white,
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: theme.spacing.xl,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderStyle: 'dashed',
  },
  emptyText: {
    color: theme.colors.secondaryText,
    marginTop: theme.spacing.md,
    textAlign: 'center',
  },
  inputSection: {
    marginBottom: theme.spacing.xl,
  },
  input: {
    backgroundColor: theme.colors.input,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    height: 120,
    textAlignVertical: 'top',
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
