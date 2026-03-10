import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getRectifiedFaults } from '../services/api';
import { theme } from '../styles/theme';
import { usePermissions } from '../hooks/usePermissions';
import FaultList from '../components/FaultList';

export default function RectifiedFaultsScreen() {
  const navigation = useNavigation();
  const { hasPermission } = usePermissions();

  // "Clear" uses the 'noc-clear-faults-clear' permission (from web controller)
  // "Revoke" is technically also part of the clear process (reopening), often guarded by the same permission
  // or a specific one. Based on NocClearFaultsController, 'noc-clear-faults-clear' covers 'revoke'.
  // Let's use that, or fallback to checking the role if the permission name is strictly just 'clear'.
  
  const canClear = hasPermission('noc-clear-faults-clear');
  const canRevoke = hasPermission('noc-clear-faults-clear'); 

  const renderActions = (item) => (
    <View style={styles.actionContainer}>
      {canClear && (
        <TouchableOpacity 
          style={[styles.actionBtn, styles.clearBtn]} 
          onPress={() => navigation.navigate('ClearFault', { fault: item })}
        >
          <Text style={styles.actionBtnText}>Clear</Text>
        </TouchableOpacity>
      )}
      {canRevoke && (
        <TouchableOpacity 
          style={[styles.actionBtn, styles.revokeBtn]} 
          onPress={() => navigation.navigate('RevokeFault', { fault: item })}
        >
          <Text style={styles.actionBtnText}>Revoke</Text>
        </TouchableOpacity>
      )}
    </View>
  );

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Rectified Faults</Text>
      </View>
      <FaultList
        fetchData={getRectifiedFaults}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
        emptyMessage="No rectified faults found."
        renderExtra={renderActions}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text },
  actionContainer: { flexDirection: 'row', gap: 8, marginTop: 8 },
  actionBtn: { paddingVertical: 6, paddingHorizontal: 12, borderRadius: 4, alignItems: 'center' },
  clearBtn: { backgroundColor: theme.colors.success },
  revokeBtn: { backgroundColor: theme.colors.danger },
  actionBtnText: { color: theme.colors.white, fontWeight: '600', fontSize: 12 },
});
