import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { getAssessments } from '../services/api';
import { theme } from '../styles/theme';
import FaultList from '../components/FaultList';

export default function AssessmentsScreen() {
  const navigation = useNavigation();

  const renderAssessButton = (item) => (
    <TouchableOpacity 
      style={styles.actionBtn} 
      onPress={() => navigation.navigate('AssessFault', { fault: item })}
    >
      <Text style={styles.actionBtnText}>Assess</Text>
    </TouchableOpacity>
  );

  return (
    <SafeAreaView style={styles.container} edges={["top", "left", "right"]}>
      <View style={styles.header}>
        <Text style={styles.headerTitle}>Assessments</Text>
      </View>
      <FaultList
        fetchData={getAssessments}
        onPressItem={(item) => navigation.navigate('FaultDetail', { id: item.id })}
        emptyMessage="No pending assessments."
        renderExtra={renderAssessButton}
      />
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  header: { padding: theme.spacing.lg },
  headerTitle: { fontSize: theme.fontSizes.xxl, fontWeight: '700', color: theme.colors.dark },
  actionBtn: { marginTop: 8, backgroundColor: theme.colors.primary, padding: 8, borderRadius: 4, alignItems: 'center', alignSelf: 'flex-start' },
  actionBtnText: { color: theme.colors.white, fontWeight: '600', fontSize: 12 },
});
