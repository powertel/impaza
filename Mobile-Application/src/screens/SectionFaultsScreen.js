import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getSectionFaults } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';

export default function SectionFaultsScreen() {
  const navigation = useNavigation();

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Section Faults</Text>
      </View>
      <FaultList
        fetchData={getSectionFaults}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id, hideActions: true })}
        emptyMessage="No faults found in section."
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text },
});
