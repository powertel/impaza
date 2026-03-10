import React, { useEffect, useState, useContext } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, TextInput, ActivityIndicator, RefreshControl, StatusBar } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { setAuthToken, getProfile, updateProfile, changePassword } from '../services/api';
import { theme } from '../styles/theme';
import { UserContext } from '../context/UserContext';
import { FontAwesome, Feather } from '@expo/vector-icons';
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
  const [isEditing, setIsEditing] = useState(false);
  const [isChangingPw, setIsChangingPw] = useState(false);

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

  const getInitials = (nameStr) => {
    if (!nameStr) return 'U';
    const parts = nameStr.split(' ');
    if (parts.length > 1) return (parts[0][0] + parts[parts.length-1][0]).toUpperCase();
    return parts[0][0].toUpperCase();
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
        setIsEditing(false);
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
        setIsChangingPw(false);
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
    <View style={styles.screen}>
      <StatusBar barStyle="light-content" backgroundColor={theme.colors.background} />
      <SafeAreaView style={{ flex: 1, paddingTop: insets.top }} edges={['top','left','right']}>
        <ScrollView 
          contentContainerStyle={{ paddingBottom: theme.spacing.xxl }} 
          showsVerticalScrollIndicator={false} 
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={loadProfile} tintColor={theme.colors.primary} />}
        >
          {/* Header Profile Section */}
          <View style={styles.profileHeader}>
            <View style={styles.avatarContainer}>
              <Text style={styles.avatarText}>{getInitials(name)}</Text>
            </View>
            <Text style={styles.userName}>{name}</Text>
            <Text style={styles.userRole}>{user?.role || 'Technician'}</Text>
            <Text style={styles.userDept}>Network Operations</Text>
          </View>

          {/* Contact Information Card */}
          <View style={styles.sectionCard}>
            <Text style={styles.sectionTitle}>Contact Information</Text>
            <View style={styles.contactRow}>
              <View style={styles.iconBox}>
                <Feather name="mail" size={18} color={theme.colors.primary} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.contactLabel}>Email</Text>
                <Text style={styles.contactValue}>{email}</Text>
              </View>
            </View>
            <View style={styles.divider} />
            <View style={styles.contactRow}>
              <View style={styles.iconBox}>
                <Feather name="phone" size={18} color={theme.colors.primary} />
              </View>
              <View style={{ flex: 1 }}>
                <Text style={styles.contactLabel}>Phone</Text>
                <Text style={styles.contactValue}>{phone}</Text>
              </View>
            </View>
          </View>

          {/* Menu Items */}
          <View style={styles.menuContainer}>
            <TouchableOpacity style={styles.menuItem} onPress={() => setIsEditing(!isEditing)}>
              <View style={styles.menuLeft}>
                <Feather name="user" size={20} color={theme.colors.primary} />
                <Text style={styles.menuText}>Edit Profile</Text>
              </View>
              <Feather name={isEditing ? "chevron-up" : "chevron-right"} size={20} color={theme.colors.secondaryText} />
            </TouchableOpacity>

            {isEditing && (
              <View style={styles.editForm}>
                <Text style={styles.label}>Full Name</Text>
                <TextInput style={styles.input} value={name} onChangeText={setName} placeholder="Your name" placeholderTextColor={theme.colors.muted} />
                <Text style={styles.label}>Phone Number</Text>
                <TextInput style={styles.input} value={phone} onChangeText={setPhone} placeholder="Phone" keyboardType="phone-pad" placeholderTextColor={theme.colors.muted} />
                <TouchableOpacity style={styles.saveBtn} onPress={handleSaveProfile} disabled={saving}>
                  {saving ? <ActivityIndicator color="#fff" /> : <Text style={styles.saveBtnText}>Save Changes</Text>}
                </TouchableOpacity>
              </View>
            )}

            <TouchableOpacity style={styles.menuItem} onPress={() => setIsChangingPw(!isChangingPw)}>
              <View style={styles.menuLeft}>
                <Feather name="lock" size={20} color={theme.colors.primary} />
                <Text style={styles.menuText}>Change Password</Text>
              </View>
              <Feather name={isChangingPw ? "chevron-up" : "chevron-right"} size={20} color={theme.colors.secondaryText} />
            </TouchableOpacity>

            {isChangingPw && (
              <View style={styles.editForm}>
                <Text style={styles.label}>New Password</Text>
                <View style={styles.inputContainer}>
                  <TextInput
                    style={[styles.input, { paddingRight: 40 }]}
                    value={newPassword}
                    onChangeText={setNewPassword}
                    secureTextEntry={!newPasswordVisible}
                    placeholder="New password"
                    placeholderTextColor={theme.colors.muted}
                  />
                  <TouchableOpacity style={styles.eyeBtn} onPress={() => setNewPasswordVisible(!newPasswordVisible)}>
                    <Feather name={newPasswordVisible ? 'eye-off' : 'eye'} size={18} color={theme.colors.secondaryText} />
                  </TouchableOpacity>
                </View>
                <PasswordStrengthMeter password={newPassword} />
                
                <Text style={styles.label}>Confirm Password</Text>
                <View style={styles.inputContainer}>
                  <TextInput
                    style={[styles.input, { paddingRight: 40 }]}
                    value={confirmPassword}
                    onChangeText={setConfirmPassword}
                    secureTextEntry={!confirmPasswordVisible}
                    placeholder="Confirm password"
                    placeholderTextColor={theme.colors.muted}
                  />
                  <TouchableOpacity style={styles.eyeBtn} onPress={() => setConfirmPasswordVisible(!confirmPasswordVisible)}>
                    <Feather name={confirmPasswordVisible ? 'eye-off' : 'eye'} size={18} color={theme.colors.secondaryText} />
                  </TouchableOpacity>
                </View>
                
                <TouchableOpacity 
                  style={[styles.saveBtn, (!isStrongPassword(newPassword) || !passwordsMatch(newPassword, confirmPassword)) && styles.disabledBtn]} 
                  onPress={handleChangePassword} 
                  disabled={pwLoading || !isStrongPassword(newPassword) || !passwordsMatch(newPassword, confirmPassword)}
                >
                  {pwLoading ? <ActivityIndicator color="#fff" /> : <Text style={styles.saveBtnText}>Update Password</Text>}
                </TouchableOpacity>
              </View>
            )}

            <TouchableOpacity style={styles.menuItem}>
              <View style={styles.menuLeft}>
                <Feather name="bell" size={20} color={theme.colors.primary} />
                <Text style={styles.menuText}>Notifications</Text>
              </View>
              <Feather name="chevron-right" size={20} color={theme.colors.secondaryText} />
            </TouchableOpacity>

            <TouchableOpacity style={styles.menuItem}>
              <View style={styles.menuLeft}>
                <Feather name="settings" size={20} color={theme.colors.primary} />
                <Text style={styles.menuText}>Preferences</Text>
              </View>
              <Feather name="chevron-right" size={20} color={theme.colors.secondaryText} />
            </TouchableOpacity>

            <TouchableOpacity style={styles.menuItem}>
              <View style={styles.menuLeft}>
                <Feather name="help-circle" size={20} color={theme.colors.primary} />
                <Text style={styles.menuText}>Help & Support</Text>
              </View>
              <Feather name="chevron-right" size={20} color={theme.colors.secondaryText} />
            </TouchableOpacity>

            <TouchableOpacity style={styles.menuItem}>
              <View style={styles.menuLeft}>
                <Feather name="info" size={20} color={theme.colors.primary} />
                <Text style={styles.menuText}>About App</Text>
              </View>
              <Feather name="chevron-right" size={20} color={theme.colors.secondaryText} />
            </TouchableOpacity>
          </View>

          {message && <Text style={styles.successMsg}>{message}</Text>}
          {error && <Text style={styles.errorMsg}>{error}</Text>}

          <TouchableOpacity style={styles.logoutBtn} onPress={logout}>
            <Feather name="power" size={20} color={theme.colors.danger} style={{ marginRight: 8 }} />
            <Text style={styles.logoutText}>Logout</Text>
          </TouchableOpacity>
          
          <Text style={styles.versionText}>Impaza Mobile v1.0.0</Text>
        </ScrollView>
      </SafeAreaView>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  profileHeader: { alignItems: 'center', marginVertical: theme.spacing.xl },
  avatarContainer: { 
    width: 100, 
    height: 100, 
    borderRadius: 50, 
    backgroundColor: theme.colors.primary, 
    justifyContent: 'center', 
    alignItems: 'center',
    marginBottom: theme.spacing.md,
    borderWidth: 4,
    borderColor: 'rgba(10, 126, 164, 0.2)'
  },
  avatarText: { fontSize: 36, fontWeight: 'bold', color: theme.colors.white },
  userName: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text, marginBottom: 4 },
  userRole: { fontSize: theme.fontSizes.md, color: theme.colors.primary, fontWeight: '600', marginBottom: 2 },
  userDept: { fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText },
  
  sectionCard: {
    backgroundColor: theme.colors.card,
    borderRadius: theme.borderRadius.lg,
    padding: theme.spacing.lg,
    marginHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.lg,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  sectionTitle: { fontSize: theme.fontSizes.md, fontWeight: '700', color: theme.colors.text, marginBottom: theme.spacing.md },
  contactRow: { flexDirection: 'row', alignItems: 'center', paddingVertical: 4 },
  iconBox: { width: 32, alignItems: 'center', marginRight: theme.spacing.sm },
  contactLabel: { fontSize: 10, color: theme.colors.secondaryText, marginBottom: 2 },
  contactValue: { fontSize: theme.fontSizes.md, color: theme.colors.text, fontWeight: '500' },
  divider: { height: 1, backgroundColor: theme.colors.border, marginVertical: theme.spacing.md },
  
  menuContainer: {
    backgroundColor: theme.colors.card,
    borderRadius: theme.borderRadius.lg,
    marginHorizontal: theme.spacing.lg,
    borderWidth: 1,
    borderColor: theme.colors.border,
    overflow: 'hidden',
    marginBottom: theme.spacing.lg,
  },
  menuItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: theme.spacing.lg,
    borderBottomWidth: 1,
    borderBottomColor: theme.colors.border,
  },
  menuLeft: { flexDirection: 'row', alignItems: 'center' },
  menuText: { fontSize: theme.fontSizes.md, color: theme.colors.text, marginLeft: theme.spacing.md, fontWeight: '500' },
  
  editForm: { padding: theme.spacing.lg, backgroundColor: theme.colors.surface, borderBottomWidth: 1, borderBottomColor: theme.colors.border },
  label: { fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText, marginBottom: 6, marginTop: 10 },
  input: { 
    backgroundColor: theme.colors.input, 
    borderWidth: 1, 
    borderColor: theme.colors.border, 
    borderRadius: theme.borderRadius.md, 
    padding: 12, 
    color: theme.colors.text,
    fontSize: theme.fontSizes.md 
  },
  inputContainer: { position: 'relative' },
  eyeBtn: { position: 'absolute', right: 12, top: 14 },
  saveBtn: { 
    backgroundColor: theme.colors.primary, 
    padding: 12, 
    borderRadius: theme.borderRadius.md, 
    alignItems: 'center', 
    marginTop: theme.spacing.lg 
  },
  saveBtnText: { color: theme.colors.white, fontWeight: '700' },
  disabledBtn: { opacity: 0.6 },
  
  logoutBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginHorizontal: theme.spacing.lg,
    padding: theme.spacing.lg,
    borderRadius: theme.borderRadius.lg,
    borderWidth: 1,
    borderColor: 'rgba(239, 68, 68, 0.3)',
    backgroundColor: 'rgba(239, 68, 68, 0.05)',
    marginBottom: theme.spacing.lg,
  },
  logoutText: { color: theme.colors.danger, fontWeight: '700', fontSize: theme.fontSizes.md },
  versionText: { textAlign: 'center', color: theme.colors.muted, fontSize: theme.fontSizes.xs, marginBottom: theme.spacing.xl },
  
  successMsg: { color: theme.colors.success, textAlign: 'center', marginBottom: 10, fontWeight: '600' },
  errorMsg: { color: theme.colors.danger, textAlign: 'center', marginBottom: 10, fontWeight: '600' },
});

