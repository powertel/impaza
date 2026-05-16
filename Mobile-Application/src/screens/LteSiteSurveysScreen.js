import React, { useEffect, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, RefreshControl } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { Feather } from '@expo/vector-icons';
import { getLteSiteSurveys } from '../services/api';
import { theme } from '../styles/theme';

export default function LteSiteSurveysScreen() {
  const navigation = useNavigation();
  const [loading, setLoading] = useState(false);
  const [items, setItems] = useState([]);

  const load = async () => {
    setLoading(true);
    try {
      const res = await getLteSiteSurveys();
      const data = res?.data || [];
      setItems(Array.isArray(data) ? data : []);
    } catch (e) {
      setItems([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    navigation.setOptions({ title: 'LTE Site Surveys' });
    load();
  }, []);

  const badgeStyle = (status) => {
    if (status === 'submitted') return { backgroundColor: 'rgba(34, 197, 94, 0.12)', borderColor: 'rgba(34, 197, 94, 0.35)', color: theme.colors.success, label: 'Submitted' };
    return { backgroundColor: 'rgba(245, 158, 11, 0.12)', borderColor: 'rgba(245, 158, 11, 0.35)', color: theme.colors.warning, label: 'Draft' };
  };

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <View style={styles.headerRow}>
        <View>
          <Text style={styles.title}>LTE Site Surveys</Text>
          <Text style={styles.subtitle}>Capture and submit LTE site survey details</Text>
        </View>
        <TouchableOpacity style={styles.newBtn} onPress={() => navigation.navigate('LteSiteSurveyWizard')}>
          <Feather name="plus" size={18} color={theme.colors.white} />
          <Text style={styles.newBtnText}>New</Text>
        </TouchableOpacity>
      </View>

      <ScrollView
        contentContainerStyle={styles.scroll}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={load} tintColor={theme.colors.primary} />}
      >
        {items.length === 0 ? (
          <View style={styles.emptyCard}>
            <Feather name="clipboard" size={22} color={theme.colors.secondaryText} />
            <Text style={styles.emptyTitle}>No surveys yet</Text>
            <Text style={styles.emptyDesc}>Create a new LTE site survey to get started.</Text>
          </View>
        ) : (
          items.map((s) => {
            const b = badgeStyle(s.status);
            return (
              <TouchableOpacity
                key={String(s.id)}
                style={styles.card}
                onPress={() => navigation.navigate('LteSiteSurveyWizard', { fromSurveyId: s.id })}
              >
                <View style={styles.cardTopRow}>
                  <Text style={styles.siteName} numberOfLines={1}>{s.site_name || 'Untitled Site'}</Text>
                  <View style={[styles.badge, { backgroundColor: b.backgroundColor, borderColor: b.borderColor }]}>
                    <Text style={[styles.badgeText, { color: b.color }]}>{b.label}</Text>
                  </View>
                </View>

                <View style={styles.metaRow}>
                  <Text style={styles.metaText}>JC: {s.jc_number || '-'}</Text>
                  <Text style={styles.metaText}>Photos: {s.photos_count ?? 0}</Text>
                </View>

                <View style={styles.metaRow}>
                  <Text style={styles.metaText}>{s.province_region || '-'}</Text>
                  <Text style={styles.metaText}>{s.survey_date || s.created_at || ''}</Text>
                </View>
              </TouchableOpacity>
            );
          })
        )}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: theme.colors.background },
  headerRow: {
    paddingHorizontal: theme.spacing.lg,
    paddingTop: theme.spacing.md,
    paddingBottom: theme.spacing.sm,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  title: { fontSize: theme.fontSizes.xl, fontWeight: '700', color: theme.colors.text },
  subtitle: { marginTop: 2, fontSize: theme.fontSizes.sm, color: theme.colors.secondaryText },
  newBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    backgroundColor: theme.colors.primary,
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: theme.borderRadius.md,
  },
  newBtnText: { color: theme.colors.white, fontWeight: '700' },
  scroll: { padding: theme.spacing.lg, paddingBottom: theme.spacing.xxl },
  card: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    marginBottom: theme.spacing.md,
  },
  cardTopRow: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', gap: 10 },
  siteName: { flex: 1, fontSize: theme.fontSizes.md, fontWeight: '700', color: theme.colors.text },
  badge: {
    borderWidth: 1,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: theme.borderRadius.circle,
  },
  badgeText: { fontSize: theme.fontSizes.xs, fontWeight: '700' },
  metaRow: { marginTop: 8, flexDirection: 'row', justifyContent: 'space-between', gap: 10 },
  metaText: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm },
  emptyCard: {
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    padding: theme.spacing.xl,
    borderWidth: 1,
    borderColor: theme.colors.border,
    alignItems: 'center',
  },
  emptyTitle: { marginTop: 10, fontWeight: '700', color: theme.colors.text, fontSize: theme.fontSizes.lg },
  emptyDesc: { marginTop: 6, color: theme.colors.secondaryText, textAlign: 'center' },
});

