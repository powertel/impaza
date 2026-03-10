import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getReferredFaults } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';

export default function ReferredFaultsScreen() {
  const navigation = useNavigation();

  const renderExtra = (item) => (
    <View style={styles.actionRow}>
      <View style={styles.tag}>
         <Text style={styles.tagText}>Referred</Text>
      </View>
      <View style={styles.btnGroup}>
        <TouchableOpacity 
          style={[styles.actionBtn, { backgroundColor: theme.colors.success, marginRight: 8 }]}
          onPress={() => navigation.navigate('CompleteReferral', { fault: item })}
        >
          <Text style={styles.actionBtnText}>Complete</Text>
        </TouchableOpacity>
        <TouchableOpacity 
          style={styles.actionBtn}
          onPress={() => navigation.navigate('AssignFault', { fault: item, mode: 'referral-reassign' })}
        >
          <Text style={styles.actionBtnText}>Reassign</Text>
        </TouchableOpacity>
      </View>
    </View>
  );

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Referred Faults</Text>
      </View>
      <FaultList
        fetchData={getReferredFaults}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
        emptyMessage="No referred faults found."
        renderExtra={renderExtra}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text },
  actionRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginTop: 8 },
  tag: { backgroundColor: 'rgba(139, 92, 246, 0.15)', borderWidth: 1, borderColor: 'rgba(139, 92, 246, 0.3)', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 12 },
  tagText: { color: theme.colors.referred, fontSize: 10, fontWeight: 'bold' },
  btnGroup: { flexDirection: 'row' },
  actionBtn: { backgroundColor: theme.colors.primary, paddingHorizontal: 12, paddingVertical: 6, borderRadius: 6 },
  actionBtnText: { color: theme.colors.white, fontSize: 12, fontWeight: '600' }
});
