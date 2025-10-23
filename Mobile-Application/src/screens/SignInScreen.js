import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, ScrollView } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { AntDesign, FontAwesome } from '@expo/vector-icons';
import { login } from '../services/api';
import { theme } from '../styles/theme';

export default function SignInScreen() {
  const navigation = useNavigation();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const handleLogin = async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await login(email, password);
      if (res?.token) {
        navigation.replace('Main');
      } else {
        setError('Invalid credentials');
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
            <View style={styles.field}> 
              <TextInput
                placeholder="Password"
                style={styles.input}
                secureTextEntry
                value={password}
                onChangeText={setPassword}
              />
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
  screen: { flex: 1, backgroundColor: theme.colors.background },
  wrapper: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: theme.spacing.lg },
  brand: { fontSize: 28, color: theme.colors.primary, fontWeight: '800', marginBottom: theme.spacing.lg },
  card: { width: '100%', maxWidth: 380, backgroundColor: theme.colors.white, borderRadius: 24, padding: 24, shadowColor: theme.colors.black, shadowOpacity: 0.1, shadowRadius: 12, shadowOffset: { width: 0, height: 4 }, elevation: 4 },
  title: { fontSize: theme.fontSizes.xl, fontWeight: '700', color: theme.colors.black, textAlign: 'center', marginBottom: theme.spacing.lg },
  field: { marginBottom: theme.spacing.md },
  input: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.md, padding: theme.spacing.md, fontSize: theme.fontSizes.md, backgroundColor: theme.colors.background },
  primaryBtn: { backgroundColor: theme.colors.primary, borderRadius: theme.spacing.md, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.sm },
  primaryBtnText: { color: theme.colors.white, fontSize: theme.fontSizes.md, fontWeight: '700' },
  subtle: { textAlign: 'center', color: theme.colors.gray, marginTop: theme.spacing.lg },
  socialRow: { flexDirection: 'row', justifyContent: 'center', gap: theme.spacing.md, marginTop: theme.spacing.sm },
  socialBtn: { borderWidth: 1, borderColor: theme.colors.lightGray, borderRadius: theme.spacing.md, padding: theme.spacing.md, backgroundColor: theme.colors.background },
  link: { marginTop: theme.spacing.lg, color: theme.colors.primary, textAlign: 'center', fontWeight: '600' },
  error: { color: theme.colors.danger, marginBottom: theme.spacing.sm, textAlign: 'center' }
});