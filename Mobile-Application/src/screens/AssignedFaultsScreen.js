import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getAssignedFaults } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';
import { usePermissions } from '../hooks/usePermissions';
import { Feather } from '@expo/vector-icons';

export default function AssignedFaultsScreen() {
  const navigation = useNavigation();
  const { hasPermission } = usePermissions();

  const renderExtra = (item) => {
    if (hasPermission('re-assign-fault')) {
      return (
        <View style={styles.actionRow}>
          <TouchableOpacity 
            style={styles.actionBtn}
            onPress={() => navigation.navigate('AssignFault', { fault: item, mode: 'reassign' })}
          >
            <Feather name="user-check" size={14} color={theme.colors.white} style={{ marginRight: 6 }} />
            <Text style={styles.actionBtnText}>Reassign</Text>
          </TouchableOpacity>
        </View>
      );
    }
    return null;
  };

  return (
    <View style={styles.screen}>
      <SafeAreaView style={{ flex: 1 }} edges={["top", "left", "right"]}>
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
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text },
  actionRow: { flexDirection: 'row', justifyContent: 'flex-end', marginTop: 12 },
  actionBtn: { 
    backgroundColor: theme.colors.primary, 
    paddingHorizontal: 12, 
    paddingVertical: 8, 
    borderRadius: theme.borderRadius.md,
    flexDirection: 'row',
    alignItems: 'center'
  },
  actionBtnText: { color: theme.colors.white, fontSize: 12, fontWeight: '600' }
});
