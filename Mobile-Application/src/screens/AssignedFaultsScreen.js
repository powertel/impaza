import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getAssignedFaults } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';
import { usePermissions } from '../hooks/usePermissions';

export default function AssignedFaultsScreen() {
  const navigation = useNavigation();
  const { hasPermission } = usePermissions();

  const renderExtra = (item) => {
    // Show 'Reassign' button if user has permission
    if (hasPermission('re-assign-fault')) {
      return (
        <View style={styles.actionRow}>
          <TouchableOpacity 
            style={styles.actionBtn}
            onPress={() => navigation.navigate('AssignFault', { fault: item, mode: 'reassign' })}
          >
            <Text style={styles.actionBtnText}>Reassign</Text>
          </TouchableOpacity>
        </View>
      );
    }
    return null;
  };

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Assigned Faults</Text>
      </View>
      <FaultList
        fetchData={getAssignedFaults}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
        emptyMessage="No assigned faults found."
        renderExtra={renderExtra}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark },
  actionRow: { flexDirection: 'row', justifyContent: 'flex-end', marginTop: 8 },
  actionBtn: { backgroundColor: theme.colors.primary, paddingHorizontal: 12, paddingVertical: 6, borderRadius: 6 },
  actionBtnText: { color: theme.colors.white, fontSize: 12, fontWeight: '600' }
});
