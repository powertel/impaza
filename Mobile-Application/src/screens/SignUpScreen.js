import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { FontAwesome } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { register } from '../services/api';
import { theme } from '../styles/theme';
import { isStrongPassword, passwordsMatch, POLICY_HINT } from '../utils/password';
import PasswordStrengthMeter from '../components/PasswordStrengthMeter';

export default function SignUpScreen() {
  const navigation = useNavigation();
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [confirm, setConfirm] = useState('');
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [confirmVisible, setConfirmVisible] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleRegister = async () => {
    // Guard client-side: enforce password policy and confirmation match
    if (!isStrongPassword(password)) {
      setError('Password does not meet the required format');
      return;
    }
    if (!passwordsMatch(password, confirm)) {
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
          <TextInput placeholder="Full Name" placeholderTextColor={theme.colors.gray} style={styles.input} value={name} onChangeText={setName} />
        </View>
        <View style={styles.field}> 
          <TextInput placeholder="Email" placeholderTextColor={theme.colors.gray} style={styles.input} autoCapitalize="none" keyboardType="email-address" value={email} onChangeText={setEmail} />
        </View>
        <View style={styles.field}> 
          <View style={styles.inputContainer}>
            <TextInput
              placeholder="Password"
              placeholderTextColor={theme.colors.gray}
              style={[styles.input, styles.passwordInput]}
              secureTextEntry={!passwordVisible}
              value={password}
              onChangeText={setPassword}
            />
            <TouchableOpacity style={styles.eyeToggleInside} onPress={() => setPasswordVisible(v => !v)}>
              <FontAwesome name={passwordVisible ? 'eye-slash' : 'eye'} size={theme.fontSizes.lg} color={theme.colors.dark} />
            </TouchableOpacity>
          </View>
          <PasswordStrengthMeter password={password} />
          <Text style={styles.hint}>{POLICY_HINT}</Text>
        </View>
        <View style={styles.field}> 
          <View style={styles.inputContainer}>
            <TextInput
              placeholder="Confirm Password"
              placeholderTextColor={theme.colors.gray}
              style={[styles.input, styles.passwordInput]}
              secureTextEntry={!confirmVisible}
              value={confirm}
              onChangeText={setConfirm}
            />
            <TouchableOpacity style={styles.eyeToggleInside} onPress={() => setConfirmVisible(v => !v)}>
              <FontAwesome name={confirmVisible ? 'eye-slash' : 'eye'} size={theme.fontSizes.lg} color={theme.colors.dark} />
            </TouchableOpacity>
          </View>
          {!passwordsMatch(password, confirm) && !!confirm ? (
            <Text style={styles.mismatch}>Passwords do not match</Text>
          ) : null}
        </View>

        {error ? <Text style={styles.error}>{error}</Text> : null}

        <TouchableOpacity style={[styles.primaryBtn, (!isStrongPassword(password) || !passwordsMatch(password, confirm)) ? styles.btnDisabled : null]} onPress={handleRegister} disabled={loading || !isStrongPassword(password) || !passwordsMatch(password, confirm)}>
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
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.sm, padding: theme.spacing.md, fontSize: theme.fontSizes.md, color: theme.colors.text, backgroundColor: theme.colors.input },
  inputContainer: { position: 'relative' },
  passwordInput: { paddingRight: theme.spacing.xl },
  eyeToggleInside: { position: 'absolute', right: theme.spacing.md, top: 0, bottom: 0, justifyContent: 'center' },
  hint: { color: theme.colors.gray, marginTop: theme.spacing.xs, fontSize: theme.fontSizes.sm },
  mismatch: { color: theme.colors.danger, marginTop: theme.spacing.xs, fontSize: theme.fontSizes.sm },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.sm, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.sm },
  btnDisabled: { opacity: 0.6 },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '600' },
  link: { marginTop: theme.spacing.lg, color: theme.colors.primary, textAlign: 'center' },
  error: { color: theme.colors.danger, marginBottom: theme.spacing.sm, textAlign: 'center' }
});