import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { register } from '../services/api';
import { theme } from '../styles/theme';

export default function SignUpScreen() {
  const navigation = useNavigation();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleRegister = async () => {
    if (password !== confirm) {
      setError('Passwords do not match');
      return;
    }
    setLoading(true);
    setError(null);
    try {
      const res = await register({ name, email, password });
      if (res?.success) {
        navigation.replace('SignIn');
      } else {
        setError(res?.message || 'Unable to register');
      }
    } catch (e) {
      setError('Unable to register');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.container} edges={["top","left","right"]}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} keyboardShouldPersistTaps="handled">
        <Text style={styles.brand}>Powertel</Text>
        <Text style={styles.title}>Create an Account</Text>

        <View style={styles.field}> 
          <TextInput placeholder="Full Name" style={styles.input} value={name} onChangeText={setName} />
        </View>
        <View style={styles.field}> 
          <TextInput placeholder="Email" style={styles.input} autoCapitalize="none" keyboardType="email-address" value={email} onChangeText={setEmail} />
        </View>
        <View style={styles.field}> 
          <TextInput placeholder="Password" style={styles.input} secureTextEntry value={password} onChangeText={setPassword} />
        </View>
        <View style={styles.field}> 
          <TextInput placeholder="Confirm Password" style={styles.input} secureTextEntry value={confirm} onChangeText={setConfirm} />
        </View>

        {error ? <Text style={styles.error}>{error}</Text> : null}

        <TouchableOpacity style={styles.primaryBtn} onPress={handleRegister} disabled={loading}>
          <Text style={styles.primaryBtnText}>{loading ? 'Creating…' : 'Continue'}</Text>
        </TouchableOpacity>

        <TouchableOpacity onPress={() => navigation.navigate('SignIn')}>
          <Text style={styles.link}>Already have an account? Sign In</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, padding: theme.spacing.xl, backgroundColor: theme.colors.white },
  brand: { fontSize: theme.fontSizes.xxl, color: theme.colors.primary, fontWeight: '700', textAlign: 'center', marginTop: theme.spacing.xl },
  title: { fontSize: theme.fontSizes.xl, fontWeight: '600', marginTop: theme.spacing.sm, marginBottom: theme.spacing.xl, textAlign: 'center' },
  field: { marginBottom: theme.spacing.md },
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, padding: theme.spacing.md, fontSize: theme.fontSizes.md },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.sm },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' },
  link: { marginTop: theme.spacing.lg, color: theme.colors.primary, textAlign: 'center' },
  error: { color: theme.colors.danger, marginBottom: theme.spacing.sm, textAlign: 'center' }
});