import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet, SafeAreaView } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { register } from '../services/api';

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
    <SafeAreaView style={styles.container}>
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
    </SafeAreaView>
  );
}

const blue = '#0A66CC';
const styles = StyleSheet.create({
  container: { flex: 1, padding: 24, backgroundColor: '#fff' },
  brand: { fontSize: 24, color: blue, fontWeight: '700', textAlign: 'center', marginTop: 24 },
  title: { fontSize: 20, fontWeight: '600', marginTop: 8, marginBottom: 24, textAlign: 'center' },
  field: { marginBottom: 12 },
  input: { borderWidth: 1, borderColor: '#D1D5DB', borderRadius: 8, padding: 12, fontSize: 16 },
  primaryBtn: { backgroundColor: blue, borderRadius: 8, paddingVertical: 14, alignItems: 'center', marginTop: 8 },
  primaryBtnText: { color: '#fff', fontSize: 16, fontWeight: '600' },
  link: { marginTop: 16, color: blue, textAlign: 'center' },
  error: { color: '#DC2626', marginBottom: 8, textAlign: 'center' }
});