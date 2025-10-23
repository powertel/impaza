import React from 'react';
import { View, Text, StyleSheet, SafeAreaView, TextInput, TouchableOpacity } from 'react-native';
import { AntDesign } from '@expo/vector-icons';

export default function DashboardScreen() {
  return (
    <SafeAreaView style={styles.screen}>
      <View style={styles.headerRow}>
        <View>
          <Text style={styles.greeting}>Hi, Technician 👋</Text>
          <Text style={styles.subtitle}>Create a better future for yourself here</Text>
        </View>
        <TouchableOpacity style={styles.avatar}><Text style={styles.avatarText}>IM</Text></TouchableOpacity>
      </View>

      <View style={styles.searchBox}>
        <AntDesign name="search1" size={18} color="#9CA3AF" />
        <TextInput placeholder="Search..." style={styles.searchInput} />
      </View>

      <View style={styles.cardLight}>
        <View style={styles.cardRow}>
          <View style={styles.iconCircle}><AntDesign name="twitter" size={20} color="#1DA1F2" /></View>
          <View style={{flex:1}}>
            <Text style={styles.cardTitle}>System Status</Text>
            <Text style={styles.cardSub}>All services operational</Text>
          </View>
          <View style={styles.badge}><Text style={styles.badgeText}>OK</Text></View>
        </View>
      </View>

      <Text style={styles.sectionTitle}>Quick Actions</Text>
      <View style={styles.quickRow}>
        <View style={styles.quickItem}><Text style={styles.quickTitle}>My Faults</Text><Text style={styles.quickSub}>View assigned</Text></View>
        <View style={styles.quickItem}><Text style={styles.quickTitle}>Reports</Text><Text style={styles.quickSub}>Recent work</Text></View>
      </View>

      <Text style={styles.sectionTitle}>Recent Activity</Text>
      <View style={styles.cardDark}>
        <Text style={styles.darkTitle}>2 faults pending</Text>
        <View style={styles.darkRow}>
          <Text style={styles.darkChip}>High</Text>
          <Text style={styles.darkChip}>Remote</Text>
          <Text style={styles.darkChip}>Fiber</Text>
        </View>
        <TouchableOpacity style={styles.applyBtn}><Text style={styles.applyText}>Open list</Text></TouchableOpacity>
      </View>
    </SafeAreaView>
  );
}

const blue = '#0A66CC';

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#F5F7FF', padding: 16 },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 12 },
  greeting: { fontSize: 20, fontWeight: '800', color: '#111827' },
  subtitle: { color: '#6B7280', marginTop: 4 },
  avatar: { width: 40, height: 40, borderRadius: 20, backgroundColor: '#E5E7EB', alignItems: 'center', justifyContent: 'center' },
  avatarText: { fontWeight: '700', color: '#374151' },
  searchBox: { flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', borderRadius: 12, paddingHorizontal: 12, paddingVertical: 10, borderWidth: 1, borderColor: '#E5E7EB', marginBottom: 12 },
  searchInput: { marginLeft: 8, flex: 1 },
  cardLight: { backgroundColor: '#EEF2FF', borderRadius: 16, padding: 14, marginBottom: 16 },
  cardRow: { flexDirection: 'row', alignItems: 'center', gap: 12 },
  iconCircle: { width: 36, height: 36, borderRadius: 18, backgroundColor: '#E0F2FE', alignItems: 'center', justifyContent: 'center', marginRight: 10 },
  cardTitle: { fontWeight: '700', color: '#111827' },
  cardSub: { color: '#6B7280', marginTop: 2 },
  badge: { backgroundColor: '#DBEAFE', borderRadius: 12, paddingVertical: 6, paddingHorizontal: 10 },
  badgeText: { color: blue, fontWeight: '700' },
  sectionTitle: { fontSize: 16, fontWeight: '700', color: '#111827', marginBottom: 8 },
  quickRow: { flexDirection: 'row', gap: 12, marginBottom: 12 },
  quickItem: { flex: 1, backgroundColor: '#fff', borderRadius: 14, padding: 14, borderWidth: 1, borderColor: '#E5E7EB' },
  quickTitle: { fontWeight: '700', color: '#111827' },
  quickSub: { color: '#6B7280', marginTop: 2 },
  cardDark: { backgroundColor: blue, borderRadius: 16, padding: 16 },
  darkTitle: { color: '#fff', fontWeight: '800', fontSize: 16 },
  darkRow: { flexDirection: 'row', gap: 8, marginTop: 10 },
  darkChip: { color: '#C7D2FE', borderWidth: 1, borderColor: '#93C5FD', borderRadius: 12, paddingVertical: 4, paddingHorizontal: 8 },
  applyBtn: { backgroundColor: '#1E3A8A', borderRadius: 10, paddingVertical: 10, alignItems: 'center', marginTop: 12 },
  applyText: { color: '#fff', fontWeight: '700' }
});