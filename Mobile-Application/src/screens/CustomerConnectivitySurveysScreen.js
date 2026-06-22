import React, { useEffect, useRef, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, ScrollView, RefreshControl, TextInput } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { Feather } from '@expo/vector-icons';
import { getCustomerConnectivitySurveys } from '../services/api';
import { usePermissions } from '../hooks/usePermissions';
import { theme } from '../styles/theme';

function Pill({ label, active, onPress }) {
  return (
    <TouchableOpacity style={[styles.pill, active && styles.pillActive]} onPress={onPress}>
      <Text style={[styles.pillText, active && styles.pillTextActive]}>{label}</Text>
    </TouchableOpacity>
  );
}

export default function CustomerConnectivitySurveysScreen() {
  const navigation = useNavigation();
  const { hasPermission } = usePermissions();
  const canList = hasPermission('surveys-list');
  const canCreate = hasPermission('survey-create');
  const [loading, setLoading] = useState(false);
  const [items, setItems] = useState([]);
  const [q, setQ] = useState('');
  const [status, setStatus] = useState('all');
  const debounceRef = useRef(null);

  const load = async (opts = {}) => {
    setLoading(true);
    try {
      const params = {
        per_page: 50,
        ...(opts.q != null ? { q: opts.q } : {}),
        ...(opts.status && opts.status !== 'all' ? { status: opts.status } : {}),
      };
      const res = await getCustomerConnectivitySurveys(params);
      const data = res?.data || [];
      setItems(Array.isArray(data) ? data : []);
    } catch (e) {
      setItems([]);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    navigation.setOptions({ title: 'Connectivity Surveys' });
    if (canList) load();
  }, [canList]);

  useEffect(() => {
    if (!canList) return;
    if (debounceRef.current) clearTimeout(debounceRef.current);
    debounceRef.current = setTimeout(() => {
      load({ q, status });
    }, 350);
    return () => {
      if (debounceRef.current) clearTimeout(debounceRef.current);
    };
  }, [q, status]);

  const badgeStyle = (value) => {
    if (value === 'submitted') return { backgroundColor: 'rgba(34, 197, 94, 0.12)', borderColor: 'rgba(34, 197, 94, 0.35)', color: theme.colors.success, label: 'Submitted' };
    return { backgroundColor: 'rgba(245, 158, 11, 0.12)', borderColor: 'rgba(245, 158, 11, 0.35)', color: theme.colors.warning, label: 'Draft' };
  };

  if (!canList) {
    return (
      <SafeAreaView style={styles.container} edges={['bottom']}>
        <View style={styles.noPermWrap}>
          <Feather name="lock" size={22} color={theme.colors.secondaryText} />
          <Text style={styles.noPermTitle}>No access</Text>
          <Text style={styles.noPermDesc}>You don't have permission to view customer connectivity surveys.</Text>
        </View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.container} edges={['bottom']}>
      <View style={styles.headerRow}>
        <View>
          <Text style={styles.title}>Customer Connectivity Surveys</Text>
          <Text style={styles.subtitle}>Capture customer connectivity survey details</Text>
        </View>
        {canCreate ? (
          <TouchableOpacity style={styles.newBtn} onPress={() => navigation.navigate('CustomerConnectivitySurveyWizard')}>
            <Feather name="plus" size={18} color={theme.colors.white} />
            <Text style={styles.newBtnText}>New</Text>
          </TouchableOpacity>
        ) : null}
      </View>

      <View style={styles.filters}>
        <View style={styles.searchWrap}>
          <Feather name="search" size={16} color={theme.colors.secondaryText} />
          <TextInput
            value={q}
            onChangeText={setQ}
            placeholder="Search customer, account/JC, site..."
            placeholderTextColor={theme.colors.muted}
            style={styles.searchInput}
            autoCorrect={false}
            autoCapitalize="none"
          />
          {q ? (
            <TouchableOpacity onPress={() => setQ('')}>
              <Feather name="x" size={16} color={theme.colors.secondaryText} />
            </TouchableOpacity>
          ) : null}
        </View>
        <View style={styles.pillsRow}>
          <Pill label="All" active={status === 'all'} onPress={() => setStatus('all')} />
          <Pill label="Submitted" active={status === 'submitted'} onPress={() => setStatus('submitted')} />
          <Pill label="Draft" active={status === 'draft'} onPress={() => setStatus('draft')} />
        </View>
      </View>

      <ScrollView
        contentContainerStyle={styles.scroll}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={() => load({ q, status })} tintColor={theme.colors.primary} />}
      >
        {items.length === 0 ? (
          <View style={styles.emptyCard}>
            <Feather name="wifi" size={22} color={theme.colors.secondaryText} />
            <Text style={styles.emptyTitle}>No surveys yet</Text>
            <Text style={styles.emptyDesc}>Create a new customer connectivity survey to get started.</Text>
          </View>
        ) : (
          items.map((s) => {
            const b = badgeStyle(s.status);
            const title = s.customer_name || s.site_name || 'Untitled';
            return (
              <TouchableOpacity
                key={String(s.id)}
                style={styles.card}
                onPress={() => navigation.navigate('CustomerConnectivitySurveyView', { id: s.id })}
              >
                <View style={styles.cardTopRow}>
                  <Text style={styles.siteName} numberOfLines={1}>{title}</Text>
                  <View style={[styles.badge, { backgroundColor: b.backgroundColor, borderColor: b.borderColor }]}>
                    <Text style={[styles.badgeText, { color: b.color }]}>{b.label}</Text>
                  </View>
                </View>

                <View style={styles.metaRow}>
                  <Text style={styles.metaText}>Account/JC: {s.account_or_jc_number || '-'}</Text>
                  <Text style={styles.metaText}>Photos: {s.photos_count ?? 0}</Text>
                </View>

                <View style={styles.metaRow}>
                  <Text style={styles.metaText}>{s.site_name || '-'}</Text>
                  <Text style={styles.metaText}>{s.survey_date || s.created_at || ''}</Text>
                </View>

                <View style={styles.metaRow}>
                  <Text style={styles.metaText}>Captured By: {s?.user?.name || '-'}</Text>
                  <View style={styles.openRow}>
                    <Text style={styles.openText}>Open</Text>
                    <Feather name="chevron-right" size={16} color={theme.colors.secondaryText} />
                  </View>
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
  noPermWrap: { flex: 1, padding: theme.spacing.lg, alignItems: 'center', justifyContent: 'center', gap: 10 },
  noPermTitle: { fontSize: theme.fontSizes.lg, fontWeight: '800', color: theme.colors.text },
  noPermDesc: { color: theme.colors.secondaryText, textAlign: 'center' },
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
  filters: { paddingHorizontal: theme.spacing.lg, paddingBottom: theme.spacing.sm, gap: theme.spacing.sm },
  searchWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    backgroundColor: theme.colors.surface,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    paddingHorizontal: 12,
    paddingVertical: 10,
  },
  searchInput: { flex: 1, color: theme.colors.text, fontSize: theme.fontSizes.sm },
  pillsRow: { flexDirection: 'row', gap: 10 },
  pill: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: theme.borderRadius.circle,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.surface,
  },
  pillActive: { backgroundColor: 'rgba(59,130,246,0.12)', borderColor: 'rgba(59,130,246,0.35)' },
  pillText: { fontSize: theme.fontSizes.xs, fontWeight: '700', color: theme.colors.secondaryText },
  pillTextActive: { color: theme.colors.primary },
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
  metaText: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, flex: 1 },
  openRow: { flexDirection: 'row', alignItems: 'center', gap: 4 },
  openText: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, fontWeight: '700' },
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

