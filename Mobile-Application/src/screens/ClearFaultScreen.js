import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, Alert, KeyboardAvoidingView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { clearFault } from '../services/api';
import { theme } from '../styles/theme';
import { Feather } from '@expo/vector-icons';

export default function ClearFaultScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { fault } = route.params || {};
  const [remark, setRemark] = useState('');
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    navigation.setOptions({
      headerTitle: 'Clear Fault',
      headerStyle: {
        backgroundColor: theme.colors.background,
      },
      headerTintColor: theme.colors.text,
      headerShadowVisible: false,
    });
  }, []);

  const handleSubmit = async () => {
    if (!remark.trim()) {
      Alert.alert('Error', 'Please provide a remark for clearing this fault.');
      return;
    }

    setSubmitting(true);
    try {
      await clearFault(fault.id, { remark });
      Alert.alert('Success', 'Fault cleared successfully.', [
        { text: 'OK', onPress: () => navigation.goBack() }
      ]);
    } catch (e) {
      Alert.alert('Error', e.message || 'Failed to clear fault.');
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
        <ScrollView contentContainerStyle={styles.scroll}>
          <View style={styles.card}>
            <View style={styles.row}>
              <View>
                <Text style={styles.label}>Fault Reference</Text>
                <Text style={styles.value}>{fault?.fault_ref_number || fault?.ref_number || fault?.id}</Text>
              </View>
              <View style={styles.nocBadge}>
                <Text style={styles.nocText}>NOC Action</Text>
              </View>
            </View>
            
            <View style={styles.divider} />
            
            <Text style={styles.label}>Customer</Text>
            <Text style={styles.value}>{fault?.customer_name || fault?.customer || 'N/A'}</Text>
          </View>

          <View style={styles.section}>
            <Text style={styles.sectionTitle}>Clearance Remark</Text>
            <TextInput
              style={styles.input}
              placeholder="Enter clearance details..."
              placeholderTextColor={theme.colors.muted}
              value={remark}
              onChangeText={setRemark}
              multiline
              numberOfLines={5}
              textAlignVertical="top"
            />
            <Text style={styles.helperText}>
              Please provide details about the resolution verification.
            </Text>
          </View>
        </ScrollView>

        <View style={styles.footer}>
          <TouchableOpacity 
            style={[styles.submitBtn, submitting && styles.disabledBtn]} 
            onPress={handleSubmit}
            disabled={submitting}
          >
            {submitting ? (
              <ActivityIndicator color="#fff" />
            ) : (
              <View style={styles.btnContent}>
                <Feather name="check-circle" size={20} color={theme.colors.white} style={{ marginRight: 8 }} />
                <Text style={styles.submitBtnText}>Clear Fault</Text>
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
  nocBadge: {
    backgroundColor: 'rgba(34, 197, 94, 0.15)',
    paddingHorizontal: theme.spacing.sm,
    paddingVertical: 2,
    borderRadius: theme.borderRadius.sm,
    borderWidth: 1,
    borderColor: 'rgba(34, 197, 94, 0.3)',
  },
  nocText: {
    color: theme.colors.success,
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
    backgroundColor: theme.colors.success,
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
