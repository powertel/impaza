import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, SafeAreaView } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { AntDesign, FontAwesome } from '@expo/vector-icons';
import { login } from '../services/api';

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
    <SafeAreaView style={styles.screen}> 
      <View style={styles.wrapper}>
        <Text style={styles.brand}>impazamon</Text>
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

          <Text style={styles.subtle}>Or sign in with</Text>
          <View style={styles.socialRow}>
            <TouchableOpacity style={styles.socialBtn}>
              <AntDesign name="google" size={20} color="#DB4437" />
            </TouchableOpacity>
            <TouchableOpacity style={styles.socialBtn}>
              <FontAwesome name="facebook" size={20} color="#1877F2" />
            </TouchableOpacity>
            <TouchableOpacity style={styles.socialBtn}>
              <AntDesign name="twitter" size={20} color="#1DA1F2" />
            </TouchableOpacity>
          </View>

          <TouchableOpacity onPress={() => navigation.navigate('SignUp')}>
            <Text style={styles.link}>Don’t have an account? Sign Up</Text>
          </TouchableOpacity>
        </View>
      </View>
    </SafeAreaView>
  );
}

const blue = '#0A66CC';

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#EEF2FF' },
  wrapper: { flex: 1, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 16 },
  brand: { fontSize: 28, color: blue, fontWeight: '800', marginBottom: 16 },
  card: { width: '100%', maxWidth: 380, backgroundColor: '#fff', borderRadius: 24, padding: 24, shadowColor: '#000', shadowOpacity: 0.1, shadowRadius: 12, shadowOffset: { width: 0, height: 4 }, elevation: 4 },
  title: { fontSize: 20, fontWeight: '700', color: '#111827', textAlign: 'center', marginBottom: 16 },
  field: { marginBottom: 12 },
  input: { borderWidth: 1, borderColor: '#D1D5DB', borderRadius: 10, padding: 12, fontSize: 16, backgroundColor: '#F9FAFB' },
  primaryBtn: { backgroundColor: blue, borderRadius: 10, paddingVertical: 14, alignItems: 'center', marginTop: 8 },
  primaryBtnText: { color: '#fff', fontSize: 16, fontWeight: '700' },
  subtle: { textAlign: 'center', color: '#6B7280', marginTop: 16 },
  socialRow: { flexDirection: 'row', justifyContent: 'center', gap: 12, marginTop: 8 },
  socialBtn: { borderWidth: 1, borderColor: '#E5E7EB', borderRadius: 10, padding: 10, backgroundColor: '#F9FAFB' },
  link: { marginTop: 16, color: blue, textAlign: 'center', fontWeight: '600' },
  error: { color: '#DC2626', marginBottom: 8, textAlign: 'center' }
});