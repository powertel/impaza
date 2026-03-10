import React, { useMemo, useState, useContext } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView, Image, ActivityIndicator, Pressable, KeyboardAvoidingView, Platform } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { Feather } from '@expo/vector-icons';
import { login as apiLogin } from '../services/api';
import { theme } from '../styles/theme';
import { UserContext } from '../context/UserContext';

export default function SignInScreen() {
  const navigation = useNavigation();
  const { login } = useContext(UserContext);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordVisible, setPasswordVisible] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [rememberMe, setRememberMe] = useState(true);

  const canSubmit = useMemo(() => {
    return !!email.trim() && !!password;
  }, [email, password]);

  const handleLogin = async () => {
    if (!canSubmit || loading) return;
    setLoading(true);
    setError(null);
    try {
      const res = await apiLogin(email, password);
      if (res?.token) {
        login(res.user);
        navigation.replace('Main');
      } else {
        const message = res?.error || res?.message || (res?.status === 405 ? 'Method Not Allowed' : 'Invalid credentials');
        setError(typeof message === 'string' ? message : 'Invalid credentials');
      }
    } catch (e) {
      setError('Unable to sign in');
    } finally {
      setLoading(false);
    }
  };

  return (
    <SafeAreaView style={styles.screen} edges={['top', 'left', 'right']}>
      <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={{ flex: 1 }}>
        <ScrollView contentContainerStyle={styles.scroll} keyboardShouldPersistTaps="handled">
          <View style={styles.header}>
            <View style={styles.logoWrap}>
              <Image source={require('../../assets/impazamon-v2.png')} style={styles.logoType} resizeMode="contain" />
            </View>
            <Text style={styles.welcome}>Welcome back</Text>
            <Text style={styles.subtitle}>Sign in to your account</Text>
          </View>

          <View style={styles.card}>
            <Text style={styles.label}>Email Address</Text>
            <View style={styles.inputRow}>
              <Feather name="mail" size={18} color={theme.colors.secondaryText} style={styles.leftIcon} />
              <TextInput
                placeholder="john.doe@powertel.co.zw"
                style={styles.input}
                placeholderTextColor={theme.colors.muted}
                autoCapitalize="none"
                keyboardType="email-address"
                value={email}
                onChangeText={setEmail}
              />
            </View>

            <Text style={[styles.label, { marginTop: theme.spacing.md }]}>Password</Text>
            <View style={styles.inputRow}>
              <Feather name="lock" size={18} color={theme.colors.secondaryText} style={styles.leftIcon} />
              <TextInput
                placeholder="••••••••••••"
                style={styles.input}
                placeholderTextColor={theme.colors.muted}
                secureTextEntry={!passwordVisible}
                value={password}
                onChangeText={setPassword}
              />
              <TouchableOpacity
                accessibilityRole="button"
                onPress={() => setPasswordVisible((v) => !v)}
                style={styles.rightIconBtn}
              >
                <Feather name={passwordVisible ? 'eye-off' : 'eye'} size={18} color={theme.colors.secondaryText} />
              </TouchableOpacity>
            </View>

            <Pressable style={styles.rememberRow} onPress={() => setRememberMe((v) => !v)}>
              <View style={[styles.checkbox, rememberMe && styles.checkboxChecked]}>
                {rememberMe && <Feather name="check" size={14} color={theme.colors.white} />}
              </View>
              <Text style={styles.rememberText}>Remember me</Text>
            </Pressable>

            {error ? (
              <View style={styles.errorRow}>
                <Feather name="alert-circle" size={14} color={theme.colors.danger} style={{ marginRight: 6 }} />
                <Text style={styles.errorText}>{error}</Text>
              </View>
            ) : null}

            <TouchableOpacity
              style={[styles.primaryBtn, (!canSubmit || loading) && styles.primaryBtnDisabled]}
              onPress={handleLogin}
              disabled={!canSubmit || loading}
              activeOpacity={0.9}
            >
              {loading ? (
                <View style={styles.btnInner}>
                  <ActivityIndicator color={theme.colors.white} style={{ marginRight: 10 }} />
                  <Text style={styles.primaryBtnText}>Signing in...</Text>
                </View>
              ) : (
                <View style={styles.btnInner}>
                  <Feather name="log-in" size={18} color={theme.colors.white} style={{ marginRight: 10 }} />
                  <Text style={styles.primaryBtnText}>Sign In</Text>
                </View>
              )}
            </TouchableOpacity>

            {/* <TouchableOpacity onPress={() => navigation.navigate('ForgotPassword')} style={styles.linkBtn}>
              <Text style={styles.link}>Forgot Password?</Text>
            </TouchableOpacity> */}
          </View>

          <View style={styles.footer}>
            <Text style={styles.footerText}>Don't have an account?</Text>
            <Text style={styles.footerText}>Contact your administrator</Text>
          </View>
        </ScrollView>
      </KeyboardAvoidingView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  scroll: {
    flexGrow: 1,
    paddingHorizontal: theme.spacing.xl,
    paddingVertical: theme.spacing.xl,
    justifyContent: 'center',
  },
  header: { alignItems: 'center', marginBottom: theme.spacing.xl },
  logoWrap: {
    width: 240,
    height: 96,
    borderRadius: 24,
    backgroundColor: 'rgba(255, 255, 255, 0.16)',
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.12)',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: theme.spacing.lg,
  },
  logoType: { width: 220, height: 60 },
  welcome: { fontSize: theme.fontSizes.xl, fontWeight: '800', color: theme.colors.text, textAlign: 'center' },
  subtitle: { fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText, marginTop: 6, textAlign: 'center' },
  card: {
    width: '100%',
    maxWidth: 420,
    alignSelf: 'center',
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.xl,
    padding: theme.spacing.xl,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  label: {
    fontSize: theme.fontSizes.sm,
    color: theme.colors.secondaryText,
    fontWeight: '600',
    marginBottom: 8,
  },
  inputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.input,
    borderRadius: theme.borderRadius.md,
    height: 48,
    paddingHorizontal: 12,
  },
  leftIcon: { marginRight: 10 },
  input: { flex: 1, color: theme.colors.text, fontSize: theme.fontSizes.md },
  rightIconBtn: { padding: 6, marginLeft: 6 },
  rememberRow: { flexDirection: 'row', alignItems: 'center', marginTop: theme.spacing.lg },
  checkbox: {
    width: 20,
    height: 20,
    borderRadius: 6,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.input,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  checkboxChecked: { backgroundColor: theme.colors.primary, borderColor: theme.colors.primary },
  rememberText: { color: theme.colors.text, fontSize: theme.fontSizes.sm, fontWeight: '600' },
  errorRow: { flexDirection: 'row', alignItems: 'center', marginTop: theme.spacing.md },
  errorText: { color: theme.colors.danger, fontSize: theme.fontSizes.sm, fontWeight: '600', flex: 1 },
  primaryBtn: {
    backgroundColor: theme.colors.primary,
    borderRadius: theme.borderRadius.md,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: theme.spacing.lg,
  },
  primaryBtnDisabled: { opacity: 0.65 },
  btnInner: { flexDirection: 'row', alignItems: 'center' },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '800' },
  linkBtn: { marginTop: theme.spacing.lg, alignItems: 'center' },
  link: { color: theme.colors.primary, fontSize: theme.fontSizes.sm, fontWeight: '700' },
  footer: { marginTop: theme.spacing.xl, alignItems: 'center' },
  footerText: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, fontWeight: '600' },
});
