import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getUnassignedFaults } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';

export default function UnassignedFaultsScreen() {
  const navigation = useNavigation();

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Unassigned Faults</Text>
      </View>
      <FaultList
        fetchData={getUnassignedFaults}
        onPressItem={(item) => navigation.navigate('AssignFault', { fault: item, mode: 'assign' })}
        emptyMessage="No unassigned faults found."
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text },
});
