import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { setAuthToken } from '../services/api';
import { theme } from '../styles/theme';

export default function ProfileScreen() {
  const navigation = useNavigation();
  const insets = useSafeAreaInsets();

  const logout = () => {
    setAuthToken(null);
    navigation.reset({ index: 0, routes: [{ name: 'SignIn' }] });
  };

  return (
    <SafeAreaView style={[styles.screen, { paddingTop: insets.top + 2}]} edges={["top","left","right"]}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} showsVerticalScrollIndicator={false}>
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
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background, padding: theme.spacing.lg },
  header: { marginBottom: theme.spacing.md },
  title: { fontSize: theme.fontSizes.xxl, fontWeight: '800', color: theme.colors.black },
  sub: { color: theme.colors.gray, marginTop: theme.spacing.xs },
  card: { backgroundColor: theme.colors.white, borderRadius: theme.spacing.lg, padding: theme.spacing.lg, borderWidth: 1, borderColor: theme.colors.lightGray },
  row: { marginBottom: theme.spacing.sm, color: theme.colors.darkGray },
  logoutBtn: { backgroundColor: theme.colors.danger, borderRadius: theme.spacing.md, paddingVertical: theme.spacing.md, alignItems: 'center', marginTop: theme.spacing.lg },
  logoutText: { color: theme.colors.white, fontWeight: '700' }
});