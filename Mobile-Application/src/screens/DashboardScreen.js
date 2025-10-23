import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TextInput, TouchableOpacity, ScrollView } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { AntDesign } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { getTechnicianStats } from '../services/api';

export default function DashboardScreen() {
  const insets = useSafeAreaInsets();
  const navigation = useNavigation();

  const [stats, setStats] = useState({ assigned: 0, completed: 0, remaining: 0, completionRate: 0, avgResolutionSec: 0, periodLabel: '' });

  useEffect(() => {
    const load = async () => {
      try {
        const data = await getTechnicianStats();
        const assigned = data?.assigned ?? 0;
        const completed = data?.resolved ?? 0;
        const remaining = data?.remaining ?? Math.max(assigned - completed, 0);
        const completionRate = (typeof data?.completionRate === 'number') ? data.completionRate : (assigned > 0 ? Math.round((completed / assigned) * 100) : 0);
        const avgResolutionSec = data?.avgResolutionSec ?? 0;
        const periodLabel = data?.periodLabel ?? '';
        setStats({ assigned, completed, remaining, completionRate, avgResolutionSec, periodLabel });
      } catch (e) {
        // swallow
      }
    };
    load();
  }, []);

  const formatDuration = (sec) => {
    const s = Math.max(0, parseInt(sec, 10) || 0);
    const h = Math.floor(s / 3600);
    const m = Math.floor((s % 3600) / 60);
    const rem = s % 60;
    if (h > 0) return `${h} hours ${m} minutes ${rem} seconds`;
    if (m > 0) return `${m} minutes ${rem} seconds`;
    return `${rem} seconds`;
  };

  const rateText = (typeof stats.completionRate === 'number') ? `${stats.completionRate.toFixed(1)}%` : `${stats.completionRate}%`;

  return (
    <SafeAreaView style={[styles.screen, { paddingTop: insets.top + 1.5 }]} edges={['top','left','right']}>
      <ScrollView contentContainerStyle={{ paddingBottom: 24 }} showsVerticalScrollIndicator={false}>
        <View style={styles.headerRow}>
          <View>
            <Text style={styles.greeting}>Hi, Technician 👋</Text>
            <Text style={styles.subtitle}>Create a better future for yourself here</Text>
          </View>
          <TouchableOpacity style={styles.avatar}><Text style={styles.avatarText}>IM</Text></TouchableOpacity>
        </View>
  
        {/* <View style={styles.searchBox}>
          <AntDesign name="search1" size={18} color="#9CA3AF" />
          <TextInput placeholder="Search..." style={styles.searchInput} />
        </View> */}
  
        {/*        <View style={styles.cardLight}>
          <View style={styles.cardRow}>
            <View style={styles.iconCircle}><AntDesign name="twitter" size={20} color="#1DA1F2" /></View>
            <View style={{flex:1}}>
              <Text style={styles.cardTitle}>System Status</Text>
              <Text style={styles.cardSub}>All services operational</Text>
            </View>
            <View style={styles.badge}><Text style={styles.badgeText}>OK</Text></View>
          </View>
        </View> */}
  
        <Text style={styles.sectionTitle}>{stats.periodLabel ? `Technician Stats (${stats.periodLabel})` : 'Technician Stats'}</Text>
        <View style={styles.statsCard}>
          <View style={styles.statsRow}>
            <View style={styles.statItem}><Text style={styles.statLabel}>Assigned</Text><Text style={styles.statValue}>{stats.assigned}</Text></View>
            <View style={styles.statItem}><Text style={styles.statLabel}>Resolved</Text><Text style={styles.statValue}>{stats.completed}</Text></View>
          </View>
          <View style={styles.statsRow}>
            <View style={styles.statItem}><Text style={styles.statLabel}>Remaining</Text><Text style={styles.statValue}>{stats.remaining}</Text></View>
            <View style={styles.statItem}><Text style={styles.statLabel}>Completion Rate</Text><Text style={styles.statValue}>{rateText}</Text></View>
          </View>
          <View style={styles.statsRow}>
            <View style={styles.statItem}><Text style={styles.statLabel}>Avg Resolution</Text><Text style={styles.statValue}>{formatDuration(stats.avgResolutionSec)}</Text></View>
            <View style={styles.statItem}><Text style={styles.statLabel}>Open List</Text><TouchableOpacity style={styles.applyBtn} onPress={() => navigation.navigate('My Faults')}><Text style={styles.applyText}>View</Text></TouchableOpacity></View>
          </View>
        </View>
  
        <Text style={styles.sectionTitle}>Quick Actions</Text>
        <View style={styles.quickRow}>
          <View style={styles.quickItem}><Text style={styles.quickTitle}>My Faults</Text><Text style={styles.quickSub}>View assigned</Text></View>
          <View style={styles.quickItem}><Text style={styles.quickTitle}>Reports</Text><Text style={styles.quickSub}>Recent work</Text></View>
        </View>
  
        <Text style={styles.sectionTitle}>Recent Activity</Text>
        <View style={styles.cardDark}>
          <Text style={styles.darkTitle}>{stats.remaining} faults pending</Text>
          <View style={styles.darkRow}>
            <Text style={styles.darkChip}>High</Text>
            <Text style={styles.darkChip}>Remote</Text>
            <Text style={styles.darkChip}>Fiber</Text>
          </View>
          <TouchableOpacity style={styles.applyBtn} onPress={() => navigation.navigate('My Faults')}>
            <Text style={styles.applyText}>Open list</Text>
          </TouchableOpacity>
        </View>
      </ScrollView>
     </SafeAreaView>
    );
}

const blue = '#0A66CC';

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: '#F5F7FF', paddingHorizontal: 16, paddingBottom: 16 },
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
  applyText: { color: '#fff', fontWeight: '700' },
  statsCard: { backgroundColor: '#fff', borderRadius: 16, padding: 16, borderWidth: 1, borderColor: '#E5E7EB', marginBottom: 16 },
  statsRow: { flexDirection: 'row', justifyContent: 'space-between', marginBottom: 8 },
  statItem: { flex: 1 },
  statLabel: { color: '#6B7280' },
  statValue: { fontSize: 18, fontWeight: '700', color: '#111827' }
});