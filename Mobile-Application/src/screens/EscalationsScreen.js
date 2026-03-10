import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getEscalations } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';

export default function EscalationsScreen() {
  const navigation = useNavigation();

  const renderTag = () => (
    <View style={styles.tag}>
       <Text style={styles.tagText}>Escalated</Text>
    </View>
  );

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Escalations</Text>
      </View>
      <FaultList
        fetchData={getEscalations}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
        emptyMessage="No escalations found."
        renderExtra={renderTag}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.text },
  tag: { marginTop: 8, backgroundColor: 'rgba(239, 68, 68, 0.15)', borderWidth: 1, borderColor: 'rgba(239, 68, 68, 0.3)', paddingHorizontal: 8, paddingVertical: 2, borderRadius: 12, alignSelf: 'flex-start' },
  tagText: { color: theme.colors.danger, fontSize: 10, fontWeight: 'bold' }
});
