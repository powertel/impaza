import React, { useEffect, useState, useContext } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, RefreshControl } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { setAuthToken, getProfile, updateProfile, changePassword } from '../services/api';
import { theme } from '../styles/theme';
import { UserContext } from '../context/UserContext';
import { FontAwesome } from '@expo/vector-icons';
import { isStrongPassword, passwordsMatch, POLICY_HINT } from '../utils/password';
import PasswordStrengthMeter from '../components/PasswordStrengthMeter';

export default function ProfileScreen() {
  const navigation = useNavigation();
  const insets = useSafeAreaInsets();
  const { user, login } = useContext(UserContext);

  const [name, setName] = useState(user?.name || '');
  const [email, setEmail] = useState(user?.email || '');
  const [phone, setPhone] = useState(user?.phonenumber || '');
  const [loading, setLoading] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [saving, setSaving] = useState(false);
  const [pwLoading, setPwLoading] = useState(false);
  const [message, setMessage] = useState(null);
  const [error, setError] = useState(null);
  const [newPassword, setNewPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [newPasswordVisible, setNewPasswordVisible] = useState(false);
  const [confirmPasswordVisible, setConfirmPasswordVisible] = useState(false);

  const loadProfile = async () => {
    setRefreshing(true);
    let mounted = true;
    try {
      setLoading(true);
      const profile = await getProfile();
      if (mounted && profile) {
        setName(profile.name || '');
        setEmail(profile.email || '');
        setPhone(profile.phonenumber || '');
      }
    } catch (e) {
      // ignore
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
    return () => { mounted = false; };
  };

  useEffect(() => { loadProfile(); }, []);

  const logout = () => {
    setAuthToken(null);
    navigation.reset({ index: 0, routes: [{ name: 'SignIn' }] });
  };

  const handleSaveProfile = async () => {
    setSaving(true);
    setError(null);
    setMessage(null);
    try {
      const res = await updateProfile({ name, phonenumber: phone });
      if (res?.success) {
        setMessage('Profile updated');
        login({ ...(user || {}), name, phonenumber: phone, email });
      } else {
        setError(res?.message || 'Unable to update profile');
      }
    } catch (e) {
      setError('Unable to update profile');
    } finally {
      setSaving(false);
    }
  };

  const handleChangePassword = async () => {
    // Client-side enforcement
    if (!isStrongPassword(newPassword)) {
      setError('Password does not meet the required format');
      return;
    }
    if (!passwordsMatch(newPassword, confirmPassword)) {
      setError('Passwords do not match');
      return;
    }
    setPwLoading(true);
    setError(null);
    setMessage(null);
    try {
      const res = await changePassword({ newpassword: newPassword, newpassword_confirmation: confirmPassword });
      if (res?.success) {
        setMessage('Password changed successfully');
        setNewPassword('');
        setConfirmPassword('');
      } else {
        setError(res?.message || 'Unable to change password');
      }
    } catch (e) {
      setError('Unable to change password');
    } finally {
      setPwLoading(false);
    }
  };

  return (
    <SafeAreaView style={[styles.screen, { paddingTop: insets.top + 2}]} edges={["top","left","right"]}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} showsVerticalScrollIndicator={false} refreshControl={<RefreshControl refreshing={refreshing} onRefresh={loadProfile} />}>
        <View style={styles.header}> 
          <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
            <Text style={styles.title}>Profile</Text>
            <TouchableOpacity onPress={loadProfile} style={{ paddingHorizontal: 8, paddingVertical: 4 }}>
              <FontAwesome name="refresh" size={20} color={theme.colors.dark} />
            </TouchableOpacity>
          </View>
          <Text style={styles.sub}>Manage your account</Text>
        </View>

        <View style={styles.card}> 
          {loading ? (
            <ActivityIndicator color={theme.colors.primary} />
          ) : (
            <>
              <View style={styles.field}> 
                <Text style={styles.label}>Full Name</Text>
                <TextInput style={styles.input} value={name} onChangeText={setName} placeholder="Your name" placeholderTextColor={theme.colors.gray} />
              </View>
              <View style={styles.field}> 
                <Text style={styles.label}>Email</Text>
                <TextInput style={[styles.input, { backgroundColor: theme.colors.inputDisabled }]} value={email} editable={false} placeholder="Email" placeholderTextColor={theme.colors.gray} />
              </View>
              <View style={styles.field}> 
                <Text style={styles.label}>Phone Number</Text>
                <TextInput style={styles.input} value={phone} onChangeText={setPhone} placeholder="Phone" keyboardType="phone-pad" placeholderTextColor={theme.colors.gray} />
              </View>

              {message ? <Text style={styles.success}>{message}</Text> : null}
              {error ? <Text style={styles.error}>{error}</Text> : null}

              <TouchableOpacity style={styles.primaryBtn} onPress={handleSaveProfile} disabled={saving}>
                <Text style={styles.primaryBtnText}>{saving ? 'Saving…' : 'Save Changes'}</Text>
              </TouchableOpacity>
            </>
          )}
        </View>

        <View style={styles.card}> 
          <Text style={styles.sectionTitle}>Change Password</Text>
          <View style={styles.field}> 
            <Text style={styles.label}>New Password</Text>
            <View style={styles.inputContainer}>
              <TextInput
                style={[styles.input, styles.passwordInput]}
                value={newPassword}
                onChangeText={setNewPassword}
                secureTextEntry={!newPasswordVisible}
                placeholder="New password"
                placeholderTextColor={theme.colors.gray}
              />
              <TouchableOpacity style={styles.eyeToggleInside} onPress={() => setNewPasswordVisible(v => !v)}>
                <FontAwesome name={newPasswordVisible ? 'eye-slash' : 'eye'} size={theme.fontSizes.lg} color={theme.colors.dark} />
              </TouchableOpacity>
            </View>
            <PasswordStrengthMeter password={newPassword} />
            <Text style={styles.hint}>{POLICY_HINT}</Text>
          </View>
          <View style={styles.field}> 
            <Text style={styles.label}>Confirm Password</Text>
            <View style={styles.inputContainer}>
              <TextInput
                style={[styles.input, styles.passwordInput]}
                value={confirmPassword}
                onChangeText={setConfirmPassword}
                secureTextEntry={!confirmPasswordVisible}
                placeholder="Confirm password"
                placeholderTextColor={theme.colors.gray}
              />
              <TouchableOpacity style={styles.eyeToggleInside} onPress={() => setConfirmPasswordVisible(v => !v)}>
                <FontAwesome name={confirmPasswordVisible ? 'eye-slash' : 'eye'} size={theme.fontSizes.lg} color={theme.colors.dark} />
              </TouchableOpacity>
            </View>
            {!passwordsMatch(newPassword, confirmPassword) && !!confirmPassword ? (
              <Text style={styles.mismatch}>Passwords do not match</Text>
            ) : null}
          </View>
          <TouchableOpacity style={[styles.secondaryBtn, (!isStrongPassword(newPassword) || !passwordsMatch(newPassword, confirmPassword)) ? styles.btnDisabled : null]} onPress={handleChangePassword} disabled={pwLoading || !isStrongPassword(newPassword) || !passwordsMatch(newPassword, confirmPassword)}>
            <Text style={styles.secondaryBtnText}>{pwLoading ? 'Changing…' : 'Update Password'}</Text>
          </TouchableOpacity>
        </View>

        <TouchableOpacity style={styles.logoutBtn} onPress={logout}>
          <Text style={styles.logoutText}>Logout</Text>
        </TouchableOpacity>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background, padding: theme.spacing.lg },
  header: { marginBottom: theme.spacing.md },
  title: { fontSize: theme.fontSizes.xxl, fontWeight: '800', color: theme.colors.black },
  sub: { color: theme.colors.gray, marginTop: theme.spacing.xs },
  card: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.lg, padding: theme.spacing.lg, borderWidth: 1, borderColor: theme.colors.lightGray, marginBottom: theme.spacing.lg },
  field: { marginBottom: theme.spacing.md },
  label: { color: theme.colors.darkGray, marginBottom: theme.spacing.xs },
  input: { backgroundColor: theme.colors.input, padding: theme.spacing.md, borderRadius: theme.spacing.xs, borderWidth: 1, borderColor: theme.colors.border, color: theme.colors.text },
  inputContainer: { position: 'relative' },
  passwordInput: { paddingRight: theme.spacing.xl },
  eyeToggleInside: { position: 'absolute', right: theme.spacing.md, top: 0, bottom: 0, justifyContent: 'center' },
  hint: { color: theme.colors.gray, marginTop: theme.spacing.xs },
  mismatch: { color: theme.colors.danger, marginTop: theme.spacing.xs },
  success: { color: theme.colors.success, marginTop: theme.spacing.sm },
  error: { color: theme.colors.danger, marginTop: theme.spacing.sm },
  sectionTitle: { fontSize: theme.fontSizes.lg, fontWeight: '700', color: theme.colors.black, marginBottom: theme.spacing.sm },
  logoutBtn: { backgroundColor: theme.colors.danger, borderRadius: theme.spacing.md, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.lg },
  logoutText: { color: theme.colors.white, fontWeight: '700' },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.md, paddingVertical: theme.spacing.md, alignItems: 'center' },
  primaryBtnText: { color: theme.colors.white, fontWeight: '700' },
  secondaryBtn: { backgroundColor: theme.colors.dark, borderRadius: theme.spacing.md, paddingVertical: theme.spacing.md, alignItems: 'center' },
  btnDisabled: { opacity: 0.6 },
  secondaryBtnText: { color: theme.colors.white, fontWeight: '700' }
});

