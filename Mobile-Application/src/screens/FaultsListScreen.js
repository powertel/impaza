import React, { useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getMyFaults } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';

export default function FaultsListScreen() {
  const navigation = useNavigation();
  const [filter, setFilter] = useState('All'); // 'All', 'Pending', 'Resolved'

  const fetchFilteredFaults = async (params) => {
    // Pass filter status to API
    // Mapping: Pending = statuses other than 4 or 6? Or just open ones?
    // Usually 'Pending' means not resolved. 'Resolved' means status 4 or 6.
    // Let's pass a 'status_filter' param to the API
    return getMyFaults({ ...params, status_filter: filter });
  };

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>My Faults</Text>
      </View>
      
      <View style={styles.filterContainer}>
        {['All', 'Pending', 'Resolved'].map((f) => (
          <TouchableOpacity 
            key={f} 
            style={[styles.filterBtn, filter === f && styles.activeFilterBtn]} 
            onPress={() => setFilter(f)}
          >
            <Text style={[styles.filterText, filter === f && styles.activeFilterText]}>{f}</Text>
          </TouchableOpacity>
        ))}
      </View>

      <FaultList
        key={filter} // Force re-render when filter changes
        fetchData={fetchFilteredFaults}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
        emptyMessage={`No ${filter !== 'All' ? filter.toLowerCase() : ''} faults found.`}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg, paddingBottom: theme.spacing.sm },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark },
  filterContainer: { flexDirection: 'row', paddingHorizontal: theme.spacing.lg, marginBottom: theme.spacing.sm },
  filterBtn: { paddingVertical: 6, paddingHorizontal: 16, borderRadius: 20, backgroundColor: theme.colors.lightGray, marginRight: 10 },
  activeFilterBtn: { backgroundColor: theme.colors.primary },
  filterText: { fontSize: theme.fontSizes.sm, fontWeight: '600', color: theme.colors.gray },
  activeFilterText: { color: theme.colors.white },
});
