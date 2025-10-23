import React from 'react';
import { View, Text, StyleSheet, SafeAreaView, TouchableOpacity } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { setAuthToken } from '../services/api';

export default function ProfileScreen() {
  const navigation = useNavigation();

  const logout = () => {
    setAuthToken(null);
    navigation.reset({ index: 0, routes: [{ name: 'SignIn' }] });
  };

  return (
    <SafeAreaView style={styles.screen}>
      <View style={styles.header}> 
        <Text style={styles.title}>Profile</Text>
        <Text style={styles.sub}>Manage your account</Text>
      </View>

      <View style={styles.card}> 
        <Text style={styles.row}>Name: Technician</Text>
        <Text style={styles.row}>Email: technician@example.com</Text>
      </View>

      <TouchableOpacity style={styles.logoutBtn} onPress={logout}>
        <Text style={styles.logoutText}>Logout</Text>
      </TouchableOpacity>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#F5F7FF', padding: 16 },
  header: { marginBottom: 12 },
  title: { fontSize: 22, fontWeight: '800', color: '#111827' },
  sub: { color: '#6B7280', marginTop: 4 },
  card: { backgroundColor: '#fff', borderRadius: 16, padding: 16, borderWidth: 1, borderColor: '#E5E7EB' },
  row: { marginBottom: 8, color: '#374151' },
  logoutBtn: { backgroundColor: '#DC2626', borderRadius: 12, paddingVertical: 12, alignItems: 'center', marginTop: 16 },
  logoutText: { color: '#fff', fontWeight: '700' }
});