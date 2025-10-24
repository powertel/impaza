import React, { useState } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ActivityIndicator, Alert } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation, useRoute } from '@react-navigation/native';
import { addFaultRemark } from '../services/api';
import { theme } from '../styles/theme';

export default function AddRemarkScreen() {
  const navigation = useNavigation();
  const route = useRoute();
  const { id } = route.params || {};

  const [remark, setRemark] = useState('');
  const [activityName, setActivityName] = useState('');
  const [loading, setLoading] = useState(false);

  const submit = async () => {
    if (!remark.trim()) {
      Alert.alert('Validation', 'Please enter a remark.');
      return;
    }
    setLoading(true);
    try {
      await addFaultRemark(id, { remark: remark.trim(), activity_name: activityName.trim() || undefined });
      Alert.alert('Success', 'Remark added successfully.', [
        { text: 'OK', onPress: () => navigation.goBack() }
      ]);
    } catch (e) {
      Alert.alert('Error', 'Failed to add remark. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <View style={styles.section}>
        <Text style={styles.title}>Add Remark</Text>
        <Text style={styles.subtitle}>Fault ID: {id}</Text>

        <Text style={styles.label}>Remark</Text>
        <TextInput
          style={styles.inputMultiline}
          placeholder="Type your remark here"
          value={remark}
          onChangeText={setRemark}
          multiline
          numberOfLines={5}
          textAlignVertical="top"
        />

        <Text style={styles.label}>Activity (optional)</Text>
        <TextInput
          style={styles.input}
          placeholder="e.g., Site visit, Link test"
          value={activityName}
          onChangeText={setActivityName}
        />

        <TouchableOpacity style={styles.primaryBtn} onPress={submit} disabled={loading}>
          {loading ? (
            <ActivityIndicator color={theme.colors.white} />
          ) : (
            <Text style={styles.primaryBtnText}>Submit Remark</Text>
          )}
        </TouchableOpacity>

        <TouchableOpacity style={styles.secondaryBtn} onPress={() => navigation.goBack()} disabled={loading}>
          <Text style={styles.secondaryBtnText}>Cancel</Text>
        </TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background, padding: theme.spacing.lg },
  section: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.md, padding: theme.spacing.lg, elevation: 1 },
  title: { fontSize: theme.fontSizes.xl, fontWeight: '700', color: theme.colors.dark, marginBottom: theme.spacing.sm },
  subtitle: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.lg },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.gray, marginBottom: theme.spacing.xs },
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingHorizontal: theme.spacing.md, paddingVertical: theme.spacing.sm, marginBottom: theme.spacing.lg, fontSize: theme.fontSizes.md, color: theme.colors.dark },
  inputMultiline: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingHorizontal: theme.spacing.md, paddingVertical: theme.spacing.md, marginBottom: theme.spacing.lg, fontSize: theme.fontSizes.md, minHeight: 120, color: theme.colors.dark },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center' },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' },
  secondaryBtn: { backgroundColor: theme.colors.white, borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.md },
  secondaryBtnText: { color: theme.colors.dark, fontSize: theme.fontSizes.md, fontWeight: '600' }
});