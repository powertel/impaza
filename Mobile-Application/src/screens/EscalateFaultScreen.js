import React, { useState, useEffect } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Alert, ActivityIndicator, KeyboardAvoidingView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useRoute, useNavigation } from '@react-navigation/native';
import { escalateFault } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function EscalateFaultScreen() {
  const route = useRoute();
  const navigation = useNavigation();
  const { id, fault } = route.params || {}; // id might be passed directly or inside fault object
  const faultId = id || fault?.id;

  const [notes, setNotes] = useState('');
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    navigation.setOptions({
      headerTitle: 'Escalate Fault',
      headerStyle: {
        backgroundColor: theme.colors.background,
      },
      headerTintColor: theme.colors.text,
      headerShadowVisible: false,
    });
  }, []);

  const submit = async () => {
    if (!notes.trim()) {
      Alert.alert('Error', 'Please provide a reason for escalation.');
      return;
    }
    setLoading(true);
    try {
      const res = await escalateFault(faultId, { remark: notes });
      if (res?.success) {
        Alert.alert('Success', 'Fault escalated successfully.', [
          { text: 'OK', onPress: () => navigation.goBack() }
        ]);
      } else {
        Alert.alert('Error', res?.error || 'Failed to escalate fault');
      }
    } catch (e) {
      Alert.alert('Error', 'Failed to escalate fault.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <KeyboardAvoidingView 
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        style={{ flex: 1 }}
      >
        <ScrollView contentContainerStyle={styles.scroll}>
          <View style={styles.card}>
            <View style={styles.row}>
              <View>
                <Text style={styles.label}>Fault Reference</Text>
                <Text style={styles.value}>{fault?.fault_ref_number || fault?.ref_number || faultId}</Text>
              </View>
              <View style={styles.escalateBadge}>
                <Feather name="alert-triangle" size={12} color={theme.colors.danger} style={{ marginRight: 4 }} />
                <Text style={styles.escalateText}>Escalation</Text>
              </View>
            </View>
            
            <View style={styles.divider} />
            
            <Text style={styles.label}>Customer</Text>
            <Text style={styles.value}>{fault?.customer_name || fault?.customer || 'N/A'}</Text>
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Reason for Escalation</Text>
            <TextInput
              placeholder="Provide context for why this fault needs escalation..."
              style={styles.input}
              multiline
              numberOfLines={5}
              value={notes}
              onChangeText={setNotes}
              placeholderTextColor={theme.colors.muted}
              textAlignVertical="top"
            />
            <Text style={styles.helperText}>
              This will notify higher-level technicians or management.
            </Text>
          </View>
        </ScrollView>

        <View style={styles.footer}>
          <TouchableOpacity 
            style={[styles.submitBtn, (loading || !notes.trim()) && styles.disabledBtn]} 
            onPress={submit} 
            disabled={loading || !notes.trim()}
          >
             {loading ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <View style={styles.btnContent}>
                <Feather name="alert-triangle" size={20} color={theme.colors.white} style={{ marginRight: 8 }} />
                <Text style={styles.submitBtnText}>Escalate Fault</Text>
              </View>
            )}
          </TouchableOpacity>
        </View>
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
    paddingBottom: 100
  },
  card: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
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
  escalateBadge: {
    backgroundColor: 'rgba(239, 68, 68, 0.15)',
    paddingHorizontal: theme.spacing.sm,
    paddingVertical: 2,
    borderRadius: theme.borderRadius.sm,
    borderWidth: 1,
    borderColor: 'rgba(239, 68, 68, 0.3)',
    flexDirection: 'row',
    alignItems: 'center',
  },
  escalateText: {
    color: theme.colors.danger,
    fontSize: 12,
    fontWeight: '600',
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
  helperText: {
    fontSize: theme.fontSizes.sm,
    color: theme.colors.secondaryText,
    marginTop: theme.spacing.sm,
    fontStyle: 'italic',
  },
  footer: {
    padding: theme.spacing.lg,
    backgroundColor: theme.colors.surface,
    borderTopWidth: 1,
    borderTopColor: theme.colors.border,
  },
  submitBtn: {
    backgroundColor: theme.colors.danger,
    paddingVertical: theme.spacing.lg,
    borderRadius: theme.borderRadius.md,
    alignItems: 'center',
  },
  disabledBtn: {
    opacity: 0.6,
  },
  btnContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  submitBtnText: {
    color: theme.colors.white,
    fontSize: theme.fontSizes.lg,
    fontWeight: '600',
  },
});
