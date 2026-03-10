import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getSectionFaults } from '../services/api';
import { theme } from '../styles/theme';
import { usePermissions } from '../hooks/usePermissions';
import FaultList from '../components/FaultList';

export default function SectionFaultsScreen() {
  const navigation = useNavigation();
  const { hasPermission } = usePermissions();
  const canReassign = hasPermission('re-assign-fault');

  const renderReassignButton = (item) => {
    if (!canReassign) return null;
    return (
       <TouchableOpacity 
         style={styles.reassignBtn} 
         onPress={() => navigation.navigate('AssignFault', { fault: item, mode: 'reassign' })}
       >
         <Text style={styles.reassignBtnText}>Reassign</Text>
       </TouchableOpacity>
    );
  };

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Section Faults</Text>
      </View>
      <FaultList
        fetchData={getSectionFaults}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
        emptyMessage="No faults found in section."
        renderExtra={renderReassignButton}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark },
  reassignBtn: { marginTop: 12, backgroundColor: theme.colors.lightGray, padding: 8, borderRadius: 4, alignItems: 'center' },
  reassignBtnText: { color: theme.colors.dark, fontWeight: '600', fontSize: 12 }
});
