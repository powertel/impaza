import React, { useState, useContext } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { AntDesign, FontAwesome } from '@expo/vector-icons';
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

  const handleLogin = async () => {
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
    <SafeAreaView style={styles.screen} edges={["top","left","right"]}> 
      <ScrollView contentContainerStyle={{ flexGrow: 1 }} keyboardShouldPersistTaps="handled">
        <View style={styles.wrapper}>
          <View style={styles.bgIcons} pointerEvents="none">
            <FontAwesome name="bolt" size={72} style={[styles.bgIcon, styles.icon1]} />
            <FontAwesome name="wrench" size={64} style={[styles.bgIcon, styles.icon2]} />
            <FontAwesome name="cog" size={80} style={[styles.bgIcon, styles.icon3]} />
            <FontAwesome name="bolt" size={56} style={[styles.bgIcon, styles.icon4]} />
            <FontAwesome name="wrench" size={48} style={[styles.bgIcon, styles.icon5]} />
          </View>

          <Text style={styles.brand}>iMPAZAMON</Text>
          <View style={styles.card}>
            <Text style={styles.title}>Login to your Account</Text>
  
            <View style={styles.field}> 
              <TextInput
                placeholder="Email"
                style={styles.input}
                autoCapitalize="none"
                keyboardType="email-address"
                value={email}
                onChangeText={setEmail}
              />
            </View>
            <View style={[styles.field, styles.passwordContainer]}> 
              <TextInput
                placeholder="Password"
                style={[styles.input, styles.passwordInput]}
                secureTextEntry={!passwordVisible}
                value={password}
                onChangeText={setPassword}
              />
              <TouchableOpacity style={styles.eyeToggleInside} onPress={() => setPasswordVisible(v => !v)}>
                <FontAwesome name={passwordVisible ? 'eye-slash' : 'eye'} size={theme.fontSizes.lg} color={theme.colors.dark} />
              </TouchableOpacity>
            </View>
  
            {error ? <Text style={styles.error}>{error}</Text> : null}
  
            <TouchableOpacity style={styles.primaryBtn} onPress={handleLogin} disabled={loading}>
              <Text style={styles.primaryBtnText}>{loading ? 'Signing In…' : 'Sign In'}</Text>
            </TouchableOpacity>

            {/* <TouchableOpacity onPress={() => navigation.navigate('ForgotPassword')}>
              <Text style={styles.link}>Forgot Password?</Text>
            </TouchableOpacity> */}
          </View>
        </View>
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#E9F3FF' },
  wrapper: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: theme.spacing.lg, position: 'relative' },
  brand: { fontSize: 28, color: theme.colors.primary, fontWeight: '800', marginBottom: theme.spacing.lg },
  card: { width: '100%', maxWidth: 380, backgroundColor: theme.colors.white, borderRadius: 24, padding: 24, shadowColor: theme.colors.black, shadowOpacity: 0.1, shadowRadius: 12, shadowOffset: { width: 0, height: 4 }, elevation: 4 },
  title: { fontSize: theme.fontSizes.xl, fontWeight: '700', color: theme.colors.black, textAlign: 'center', marginBottom: theme.spacing.lg },
  field: { marginBottom: theme.spacing.md },
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.md, padding: theme.spacing.md, fontSize: theme.fontSizes.md, backgroundColor: theme.colors.background },
  passwordRow: { flexDirection: 'row', alignItems: 'center' },
  passwordContainer: { position: 'relative' },
  passwordInput: { paddingRight: theme.spacing.xl },
  eyeToggle: { marginLeft: theme.spacing.sm, padding: theme.spacing.xs },
  eyeToggleInside: { position: 'absolute', right: theme.spacing.md, top: 0, bottom: 0, justifyContent: 'center' },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.md, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.sm },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '700' },
  subtle: { textAlign: 'center', color: theme.colors.gray, marginTop: theme.spacing.lg },
  socialRow: { flexDirection: 'row', justifyContent: 'center', gap: theme.spacing.md, marginTop: theme.spacing.sm },
  socialBtn: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.md, padding: theme.spacing.md, backgroundColor: theme.colors.background },
  link: { marginTop: theme.spacing.lg, color: theme.colors.primary, textAlign: 'center', fontWeight: '600' },
  error: { color: theme.colors.danger, marginBottom: theme.spacing.sm, textAlign: 'center' },
  bgIcons: { position: 'absolute', top: 0, left: 0, right: 0, bottom: 0 },
  bgIcon: { position: 'absolute', color: theme.colors.primary, opacity: 0.2 },
  icon1: { top: 40, left: 30, transform: [{ rotate: '15deg' }] },
  icon2: { bottom: 60, right: 40, transform: [{ rotate: '-20deg' }] },
  icon3: { top: 160, right: 80, transform: [{ rotate: '35deg' }] },
  icon4: { bottom: 140, left: 70, transform: [{ rotate: '5deg' }] },
  icon5: { top: 300, left: 220, transform: [{ rotate: '-10deg' }] }
});