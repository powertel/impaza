import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getReferredFaults } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';

export default function ReferredFaultsScreen() {
  const navigation = useNavigation();

  const renderTag = () => (
    <View style={styles.tag}>
       <Text style={styles.tagText}>Referred</Text>
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
        renderExtra={renderTag}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark },
  tag: { marginTop: 8, backgroundColor: '#FEF3C7', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 12, alignSelf: 'flex-start' },
  tagText: { color: theme.colors.warning, fontSize: 10, fontWeight: 'bold' }
});
