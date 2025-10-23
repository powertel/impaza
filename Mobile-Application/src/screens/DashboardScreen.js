import React, { useEffect, useState, useContext } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { AntDesign, Feather } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { getTechnicianStats } from '../services/api';
import { theme } from '../styles/theme';
import { UserContext } from '../context/UserContext';

export default function DashboardScreen() {
  const insets = useSafeAreaInsets();
  const navigation = useNavigation();
  const { user } = useContext(UserContext);

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
    if (h > 0) return `${h}h ${m}m ${rem}s`;
    if (m > 0) return `${m}m ${rem}s`;
    return `${rem}s`;
  };

  const getInitials = (name) => {
    if (!name) return '';
    const names = name.split(' ');
    if (names.length > 1) {
      return `${names[0][0]}${names[names.length - 1][0]}`.toUpperCase();
    }
    return `${names[0][0]}`.toUpperCase();
  };

  const rateText = (typeof stats.completionRate === 'number') ? `${stats.completionRate.toFixed(1)}%` : `${stats.completionRate}%`;

  const StatCard = ({ icon, label, value, color }) => (
    <View style={styles.statCard}>
      <Feather name={icon} size={theme.fontSizes.xl} color={color} />
      <Text style={styles.statLabel}>{label}</Text>
      <Text style={styles.statValue}>{value}</Text>
    </View>
  );

  return (
    <View style={styles.screen}>
      <SafeAreaView style={{ flex: 1, paddingTop: insets.top + 1.5 }} edges={['top','left','right']}>
        <ScrollView contentContainerStyle={{ paddingBottom: theme.spacing.lg }} showsVerticalScrollIndicator={false}>
          <View style={styles.headerRow}>
            <View>
              <Text style={styles.greeting}>Hi, {user?.name?.split(' ')[0]} 👋</Text>
              <Text style={styles.subtitle}>Here's your performance overview</Text>
            </View>
            <TouchableOpacity style={styles.avatar}><Text style={styles.avatarText}>{getInitials(user?.name)}</Text></TouchableOpacity>
          </View>

          <Text style={styles.sectionTitle}>{stats.periodLabel ? `Technician Stats (${stats.periodLabel})` : 'Technician Stats'}</Text>
          
          <View style={styles.statsGrid}>
            <StatCard icon="clipboard" label="Assigned" value={stats.assigned} color={theme.colors.warning} />
            <StatCard icon="check-circle" label="Resolved" value={stats.completed} color={theme.colors.success} />
            <StatCard icon="alert-circle" label="Remaining" value={stats.remaining} color={theme.colors.danger} />
            <StatCard icon="pie-chart" label="Completion Rate" value={rateText} color={theme.colors.info} />
          </View>

          <View style={styles.avgResolutionCard}>
            <Feather name="clock" size={theme.fontSizes.xl} color={theme.colors.dark} />
            <Text style={styles.avgResolutionLabel}>Avg Resolution Time</Text>
            <Text style={styles.avgResolutionValue}>{formatDuration(stats.avgResolutionSec)}</Text>
          </View>

          <TouchableOpacity style={styles.mainAction} onPress={() => navigation.navigate('My Faults')}>
            <Text style={styles.mainActionText}>View My Faults</Text>
            <AntDesign name="arrowright" size={theme.fontSizes.lg} color={theme.colors.white} />
          </TouchableOpacity>
  
          <Text style={styles.sectionTitle}>Quick Actions</Text>
          <View style={styles.quickRow}>
            <TouchableOpacity style={styles.quickItem}>
              <Feather name="list" size={theme.fontSizes.xl} color={theme.colors.dark} />
              <Text style={styles.quickTitle}>All Faults</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.quickItem}>
              <Feather name="bar-chart-2" size={theme.fontSizes.xl} color={theme.colors.dark} />
              <Text style={styles.quickTitle}>Reports</Text>
            </TouchableOpacity>
          </View>
        </ScrollView>
       </SafeAreaView>
    </View>
    );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.white },
  headerRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: theme.spacing.xl, paddingHorizontal: theme.spacing.lg },
  greeting: { fontSize: theme.fontSizes.xxl, fontWeight: 'bold', color: theme.colors.dark },
  subtitle: { color: theme.colors.gray, marginTop: theme.spacing.xs, fontSize: theme.fontSizes.sm },
  avatar: { width: 40, height: 40, borderRadius: 20, backgroundColor: theme.colors.lightGray, alignItems: 'center', justifyContent: 'center' },
  avatarText: { fontWeight: 'bold', color: theme.colors.dark, fontSize: theme.fontSizes.md },
  sectionTitle: { fontSize: theme.fontSizes.lg, fontWeight: 'bold', color: theme.colors.dark, marginBottom: theme.spacing.md, paddingHorizontal: theme.spacing.lg },
  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    paddingHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.md,
  },
  statCard: {
    backgroundColor: theme.colors.veryLightGray,
    borderRadius: theme.spacing.md,
    padding: theme.spacing.md,
    width: '48%',
    marginBottom: theme.spacing.md,
    alignItems: 'center',
  },
  statLabel: { color: theme.colors.gray, fontSize: theme.fontSizes.xs, marginTop: theme.spacing.sm },
  statValue: { fontSize: theme.fontSizes.xxl, fontWeight: 'bold', color: theme.colors.dark, marginTop: theme.spacing.xs },
  avgResolutionCard: {
    backgroundColor: theme.colors.veryLightGray,
    borderRadius: theme.spacing.md,
    padding: theme.spacing.lg,
    marginHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.xl,
    alignItems: 'center',
  },
  avgResolutionLabel: { color: theme.colors.gray, fontSize: theme.fontSizes.sm, marginTop: theme.spacing.sm },
  avgResolutionValue: { fontSize: theme.fontSizes.lg, fontWeight: 'bold', color: theme.colors.dark, marginTop: theme.spacing.xs },
  mainAction: {
    backgroundColor: theme.colors.primary,
    borderRadius: theme.spacing.md,
    paddingVertical: theme.spacing.lg - 2,
    marginHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.xl,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
  },
  mainActionText: { color: theme.colors.white, fontWeight: 'bold', fontSize: theme.fontSizes.md, marginRight: theme.spacing.sm },
  quickRow: { flexDirection: 'row', justifyContent: 'space-between', paddingHorizontal: theme.spacing.lg },
  quickItem: {
    backgroundColor: theme.colors.veryLightGray,
    borderRadius: theme.spacing.md,
    padding: theme.spacing.lg,
    width: '48%',
    alignItems: 'center',
  },
  quickTitle: { fontWeight: 'bold', color: theme.colors.dark, fontSize: theme.fontSizes.sm, marginTop: theme.spacing.sm },
});