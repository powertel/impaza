import React, { useEffect, useState, useContext } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, RefreshControl, StatusBar } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { AntDesign, Feather } from '@expo/vector-icons';
import { useNavigation } from '@react-navigation/native';
import { getTechnicianStats, setAuthToken } from '../services/api';
import { theme } from '../styles/theme';
import { UserContext } from '../context/UserContext';
import { usePermissions } from '../hooks/usePermissions';

export default function DashboardScreen() {
  const insets = useSafeAreaInsets();
  const navigation = useNavigation();
  const { user, logout } = useContext(UserContext);
  const { hasPermission, hasAnyPermission } = usePermissions();

  const [stats, setStats] = useState({ assigned: 0, completed: 0, remaining: 0, completionRate: 0, avgResolutionSec: 0, periodLabel: '' });
  const [refreshing, setRefreshing] = useState(false);

  const loadStats = async () => {
    setRefreshing(true);
    try {
      const data = await getTechnicianStats();
      const assigned = data?.assigned ?? 0;
      const completed = data?.resolved ?? 0;
      const remaining = data?.remaining ?? Math.max(assigned - completed, 0);
      const completionRate = (typeof data?.completionRate === 'number') ? data.completionRate : (assigned > 0 ? Math.round((completed / assigned) * 100) : 0);
      const avgResolutionSec = data?.avgResolutionSec ?? 0;
      const periodLabel = data?.periodLabel ?? '';
      const waitingAssessment = data?.waitingAssessment ?? 0;
      setStats({ assigned, completed, remaining, completionRate, avgResolutionSec, periodLabel, waitingAssessment });
    } catch (e) {
      // swallow
    } finally {
      setRefreshing(false);
    }
  };

  useEffect(() => { loadStats(); }, []);

  const formatDuration = (sec) => {
    const s = Math.max(0, parseInt(sec, 10) || 0);
    const h = Math.floor(s / 3600);
    const m = Math.floor((s % 3600) / 60);
    const rem = s % 60;
    if (h > 0) return `${h}h ${m}m`;
    if (m > 0) return `${m}m ${rem}s`;
    return `${rem}s`;
  };

  const rateText = (typeof stats.completionRate === 'number') ? `${stats.completionRate.toFixed(0)}%` : `${stats.completionRate}%`;

  const onLogout = async () => {
    try {
      await logout();
    } catch (e) {
    }
    setAuthToken(null);
    navigation.reset({ index: 0, routes: [{ name: 'SignIn' }] });
  };

  const StatCard = ({ icon, label, value, color, borderColor }) => (
    <View style={[styles.statCard, { borderLeftColor: borderColor, borderLeftWidth: 4 }]}>
      <View style={styles.statHeader}>
        <Feather name={icon} size={18} color={color} />
        <Text style={styles.statLabel}>{label}</Text>
      </View>
      <Text style={styles.statValue}>{value}</Text>
    </View>
  );

  return (
    <View style={styles.screen}>
      <StatusBar barStyle="light-content" backgroundColor={theme.colors.background} />
      <SafeAreaView style={{ flex: 1, paddingTop: insets.top }} edges={['top','left','right']}>
        <ScrollView 
          contentContainerStyle={{ paddingBottom: theme.spacing.xxl }} 
          showsVerticalScrollIndicator={false} 
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={loadStats} tintColor={theme.colors.primary} />}
        >
          {/* Header */}
          <View style={styles.headerRow}>
            <View>
              <Text style={styles.greeting}>Hi, {user?.name?.split(' ')[0]} 👋</Text>
              <Text style={styles.subtitle}>Your performance overview</Text>
            </View>
            <TouchableOpacity onPress={loadStats} style={styles.refreshBtn}>
              <Feather name="refresh-cw" size={20} color={theme.colors.text} />
            </TouchableOpacity>
          </View>

          {/* Stats Section */}
          {hasAnyPermission(['technician-configuration', 'assigned-fault-list', 'noc-clear-faults-list', 'department-faults-list', 'chief-tech-clear-faults-list', 'my-fault-list']) && (
            <>
              <Text style={styles.sectionTitle}>Today's Stats</Text>
              
              <View style={styles.statsGrid}>
                <StatCard 
                  icon="clipboard" 
                  label="Assigned" 
                  value={stats.assigned} 
                  color={theme.colors.info} 
                  borderColor={theme.colors.info}
                />
                <StatCard 
                  icon="check-circle" 
                  label="Resolved" 
                  value={stats.completed} 
                  color={theme.colors.success}
                  borderColor={theme.colors.success} 
                />
                <StatCard 
                  icon="alert-circle" 
                  label="Remaining" 
                  value={stats.remaining} 
                  color={theme.colors.danger}
                  borderColor={theme.colors.danger} 
                />
                <StatCard 
                  icon="pie-chart" 
                  label="Completion" 
                  value={rateText} 
                  color={theme.colors.warning}
                  borderColor={theme.colors.warning} 
                />
              </View>

              <View style={styles.avgResolutionCard}>
                <View style={styles.avgHeader}>
                  <Feather name="clock" size={18} color={theme.colors.primary} />
                  <Text style={styles.avgResolutionLabel}>Average Resolution Time</Text>
                </View>
                <Text style={styles.avgResolutionValue}>{formatDuration(stats.avgResolutionSec)}</Text>
              </View>
            </>
          )}

          {/* Main Action */}
          {hasAnyPermission(['fault-list', 'my-fault-list', 'assigned-fault-list']) && (
            <TouchableOpacity style={styles.mainAction} onPress={() => navigation.navigate('Faults')}>
              <Text style={styles.mainActionText}>View My Faults</Text>
              <AntDesign name="arrowright" size={20} color={theme.colors.white} />
            </TouchableOpacity>
          )}
  
          {/* Quick Actions */}
          <Text style={styles.sectionTitle}>Quick Actions</Text>
          <View style={styles.quickRow}>
            {hasPermission('assigned-fault-list') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('AssignedFaults')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(59, 130, 246, 0.1)' }]}>
                  <Feather name="user-check" size={24} color={theme.colors.info} />
                </View>
                <Text style={styles.quickTitle}>Assigned</Text>
              </TouchableOpacity>
            )}
            {hasPermission('assigned-fault-list') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('UnassignedFaults')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(107, 114, 128, 0.1)' }]}>
                  <Feather name="inbox" size={24} color={theme.colors.gray} />
                </View>
                <Text style={styles.quickTitle}>Unassigned</Text>
              </TouchableOpacity>
            )}
            {hasAnyPermission(['department-faults-list', 'assigned-fault-list']) && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('SectionFaults')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(59, 130, 246, 0.1)' }]}>
                  <Feather name="users" size={24} color={theme.colors.info} />
                </View>
                <Text style={styles.quickTitle}>Section</Text>
              </TouchableOpacity>
            )}
            {hasPermission('fault-assessment') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('Assessments')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(16, 185, 129, 0.1)' }]}>
                  <Feather name="check-square" size={24} color={theme.colors.success} />
                </View>
                <Text style={styles.quickTitle}>Assess</Text>
              </TouchableOpacity>
            )}
            {hasPermission('noc-clear-faults-list') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('RectifiedFaults')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(34, 197, 94, 0.1)' }]}>
                  <Feather name="check-circle" size={24} color={theme.colors.success} />
                </View>
                <Text style={styles.quickTitle}>Rectified</Text>
              </TouchableOpacity>
            )}
            {hasPermission('chief-tech-clear-faults-list') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('Escalations')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(239, 68, 68, 0.1)' }]}>
                  <Feather name="alert-triangle" size={24} color={theme.colors.danger} />
                </View>
                <Text style={styles.quickTitle}>Escalations</Text>
              </TouchableOpacity>
            )}
            <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('ResolvedFaults')}>
              <View style={[styles.iconContainer, { backgroundColor: 'rgba(6, 182, 212, 0.1)' }]}>
                <Feather name="archive" size={24} color={theme.colors.primary} />
              </View>
              <Text style={styles.quickTitle}>Resolved</Text>
            </TouchableOpacity>
            {hasPermission('surveys-list') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('LteSiteSurveys')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(10, 126, 164, 0.1)' }]}>
                  <Feather name="clipboard" size={24} color={theme.colors.primary} />
                </View>
                <Text style={styles.quickTitle}>LTE Survey</Text>
              </TouchableOpacity>
            )}
            {hasPermission('surveys-list') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('CustomerConnectivitySurveys')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(16, 185, 129, 0.1)' }]}>
                  <Feather name="wifi" size={24} color={theme.colors.success} />
                </View>
                <Text style={styles.quickTitle}>Connectivity</Text>
              </TouchableOpacity>
            )}
            {hasPermission('refer-fault') && (
              <TouchableOpacity style={styles.quickItem} onPress={() => navigation.navigate('ReferredFaults')}>
                <View style={[styles.iconContainer, { backgroundColor: 'rgba(139, 92, 246, 0.1)' }]}>
                  <Feather name="share-2" size={24} color={theme.colors.referred} />
                </View>
                <Text style={styles.quickTitle}>Referred</Text>
              </TouchableOpacity>
            )}
          </View>

          {/* Pending Assessment Alert */}
          {stats.waitingAssessment > 0 && (
            <View style={styles.alertCard}>
              <View style={{ flexDirection: 'row', alignItems: 'center', marginBottom: 4 }}>
                <Feather name="alert-circle" size={20} color={theme.colors.warning} style={{ marginRight: 8 }} />
                <Text style={styles.alertTitle}>{stats.waitingAssessment} Pending Assessments</Text>
              </View>
              <Text style={styles.alertDesc}>Complete assessments to keep queue clear</Text>
            </View>
          )}
        </ScrollView>
       </SafeAreaView>
    </View>
    );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  headerRow: { 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center', 
    marginBottom: theme.spacing.xl, 
    paddingHorizontal: theme.spacing.lg 
  },
  greeting: { 
    fontSize: theme.fontSizes.xxl, 
    fontWeight: '700', 
    color: theme.colors.text 
  },
  subtitle: { 
    color: theme.colors.secondaryText, 
    marginTop: 4, 
    fontSize: theme.fontSizes.sm 
  },
  refreshBtn: {
    padding: 8,
    borderRadius: theme.borderRadius.circle,
    backgroundColor: theme.colors.surface,
  },
  sectionTitle: { 
    fontSize: theme.fontSizes.lg, 
    fontWeight: '600', 
    color: theme.colors.text, 
    marginBottom: theme.spacing.md, 
    paddingHorizontal: theme.spacing.lg 
  },
  statsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
    paddingHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.md,
  },
  statCard: {
    backgroundColor: theme.colors.card,
    borderRadius: theme.borderRadius.lg,
    padding: theme.spacing.md,
    width: '48%',
    marginBottom: theme.spacing.md,
    justifyContent: 'space-between',
    // Minimal shadow for depth
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  statHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: theme.spacing.sm,
  },
  statLabel: { 
    color: theme.colors.secondaryText, 
    fontSize: theme.fontSizes.xs, 
    marginLeft: 6,
    fontWeight: '500'
  },
  statValue: { 
    fontSize: theme.fontSizes.xxl, 
    fontWeight: '700', 
    color: theme.colors.text 
  },
  avgResolutionCard: {
    backgroundColor: theme.colors.card,
    borderRadius: theme.borderRadius.lg,
    padding: theme.spacing.lg,
    marginHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.xl,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  avgHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 8,
  },
  avgResolutionLabel: { 
    color: theme.colors.secondaryText, 
    fontSize: theme.fontSizes.sm, 
    marginLeft: 8 
  },
  avgResolutionValue: { 
    fontSize: 28, 
    fontWeight: '700', 
    color: theme.colors.text 
  },
  mainAction: {
    backgroundColor: theme.colors.primary,
    borderRadius: theme.borderRadius.lg,
    paddingVertical: theme.spacing.lg,
    marginHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.xl,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: theme.colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  mainActionText: { 
    color: theme.colors.white, 
    fontWeight: '700', 
    fontSize: theme.fontSizes.md, 
    marginRight: theme.spacing.sm 
  },
  quickRow: { 
    flexDirection: 'row', 
    flexWrap: 'wrap', 
    justifyContent: 'space-between', 
    paddingHorizontal: theme.spacing.lg 
  },
  quickItem: {
    width: '23%', // 4 items per row approximately
    alignItems: 'center',
    marginBottom: theme.spacing.xl,
  },
  iconContainer: {
    width: 56,
    height: 56,
    borderRadius: 20,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  quickTitle: { 
    fontWeight: '500', 
    color: theme.colors.secondaryText, 
    fontSize: 12,
    textAlign: 'center'
  },
  alertCard: {
    marginHorizontal: theme.spacing.lg,
    marginTop: theme.spacing.md,
    backgroundColor: 'rgba(245, 158, 11, 0.1)',
    borderWidth: 1,
    borderColor: 'rgba(245, 158, 11, 0.3)',
    borderRadius: theme.borderRadius.lg,
    padding: theme.spacing.md,
  },
  alertTitle: {
    color: theme.colors.warning,
    fontWeight: '700',
    fontSize: theme.fontSizes.md,
  },
  alertDesc: {
    color: theme.colors.secondaryText,
    fontSize: theme.fontSizes.sm,
    marginLeft: 28, // Align with text above
  }
});
