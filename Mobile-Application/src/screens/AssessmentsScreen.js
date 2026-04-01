import React, { useState, useEffect } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getAssessments } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';
import { Feather } from '@expo/vector-icons';

export default function AssessmentsScreen() {
  const navigation = useNavigation();
  const [stats, setStats] = useState({ total: 0, critical: 0 });

  // Custom header to display stats
  const ListHeader = ({ data }) => {
    // Calculate stats on the fly when data changes
    useEffect(() => {
      if (data) {
        const total = data.length;
        const critical = data.filter(i => i.priorityLevel?.toLowerCase() === 'critical').length;
        setStats({ total, critical });
      }
    }, [data]);

    return (
      <View style={styles.statsContainer}>
        <View style={styles.statCard}>
          <Text style={styles.statLabel}>Total Pending</Text>
          <Text style={styles.statValue}>{stats.total}</Text>
        </View>
        <View style={styles.statCard}>
          <Text style={[styles.statLabel, { color: theme.colors.secondaryText }]}>Critical</Text>
          <Text style={[styles.statValue, { color: theme.colors.danger }]}>{stats.critical}</Text>
        </View>
      </View>
    );
  };

  const renderAssessButton = (item) => (
    <View style={styles.cardActions}>
      <View style={styles.waitContainer}>
        <Feather name="clock" size={12} color={theme.colors.warning} />
        {/* Mock waiting time calculation */}
        <Text style={styles.waitText}>Waiting 1d</Text>
      </View>
      <TouchableOpacity 
        style={styles.actionBtn} 
        onPress={() => navigation.navigate('AssessFault', { fault: item })}
      >
        <Feather name="check-circle" size={16} color={theme.colors.white} style={{ marginRight: 8 }} />
        <Text style={styles.actionBtnText}>Start Assessment</Text>
      </TouchableOpacity>
    </View>
  );

  return (
    <View style={styles.screen}>
      <SafeAreaView style={{ flex: 1 }} edges={["top", "left", "right"]}>
        <View style={styles.header}>
          <View>
            <Text style={styles.headerTitle}>Assessments</Text>
            <Text style={styles.headerSubtitle}>{stats.total} pending</Text>
          </View>
          <TouchableOpacity style={styles.refreshBtn}>
            <Feather name="refresh-cw" size={20} color={theme.colors.text} />
          </TouchableOpacity>
        </View>

        <FaultList
          fetchData={getAssessments}
          onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
          emptyMessage="No pending assessments."
          renderExtra={renderAssessButton}
          ListHeaderComponent={<ListHeader />}
          // Pass a callback to lift state up if needed, or rely on internal data
          onDataLoaded={(data) => {
             const total = data.length;
             const critical = data.filter(i => i.priorityLevel?.toLowerCase() === 'critical').length;
             setStats({ total, critical });
          }}
        />
      </SafeAreaView>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  header: { 
    padding: theme.spacing.lg, 
    flexDirection: 'row', 
    justifyContent: 'space-between', 
    alignItems: 'center' 
  },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text },
  headerSubtitle: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, marginTop: 2 },
  refreshBtn: { padding: 8 },
  
  statsContainer: {
    flexDirection: 'row',
    paddingHorizontal: theme.spacing.lg,
    marginBottom: theme.spacing.lg,
    gap: theme.spacing.md,
  },
  statCard: {
    flex: 1,
    backgroundColor: theme.colors.card,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
  },
  statLabel: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.xs, marginBottom: 4 },
  statValue: { fontSize: 24, fontWeight: '700', color: theme.colors.text },

  cardActions: { marginTop: 12 },
  waitContainer: { 
    flexDirection: 'row', 
    alignItems: 'center', 
    justifyContent: 'flex-end', 
    marginBottom: 8 
  },
  waitText: { 
    color: theme.colors.warning, 
    fontSize: 12, 
    fontWeight: '600', 
    marginLeft: 4 
  },
  actionBtn: { 
    backgroundColor: theme.colors.primary, 
    paddingVertical: 12, 
    borderRadius: theme.borderRadius.md, 
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    width: '100%'
  },
  actionBtnText: { color: theme.colors.white, fontWeight: '700', fontSize: theme.fontSizes.md },
});
