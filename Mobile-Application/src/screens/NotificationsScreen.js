import React, { useCallback, useEffect, useMemo, useState } from 'react';
import { View, Text, StyleSheet, TouchableOpacity, FlatList, RefreshControl } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { Feather } from '@expo/vector-icons';
import { theme } from '../styles/theme';
import { getNotifications, markAllNotificationsRead, markNotificationRead } from '../services/api';

function formatTime(value) {
  try {
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  } catch (e) {
    return '';
  }
}

function formatDay(value) {
  try {
    const d = new Date(value);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleDateString([], { weekday: 'short', month: 'short', day: 'numeric' });
  } catch (e) {
    return '';
  }
}

export default function NotificationsScreen() {
  const insets = useSafeAreaInsets();
  const [items, setItems] = useState([]);
  const [refreshing, setRefreshing] = useState(false);
  const [loading, setLoading] = useState(false);

  const load = useCallback(async () => {
    setRefreshing(true);
    try {
      const res = await getNotifications({ limit: 30 });
      const raw = res?.notifications;
      const list = Array.isArray(raw) ? raw : (raw && typeof raw === 'object' ? Object.values(raw) : []);
      setItems(list);
    } catch (e) {
    } finally {
      setRefreshing(false);
    }
  }, []);

  useEffect(() => {
    setLoading(true);
    load().finally(() => setLoading(false));
  }, [load]);

  const onPressItem = async (id) => {
    try {
      await markNotificationRead(id);
    } catch (e) {
    } finally {
      load();
    }
  };

  const onMarkAll = async () => {
    try {
      await markAllNotificationsRead();
    } catch (e) {
    } finally {
      load();
    }
  };

  const sections = useMemo(() => {
    const byDay = new Map();
    for (const item of items) {
      const key = formatDay(item?.created_at) || 'Recent';
      if (!byDay.has(key)) byDay.set(key, []);
      byDay.get(key).push(item);
    }
    return Array.from(byDay.entries()).map(([title, data]) => ({ title, data }));
  }, [items]);

  const renderItem = ({ item }) => {
    const data = item?.data || {};
    const title = String(data.title || 'Notification');
    const body = String(data.body || '');
    const unread = !item?.read_at;
    const time = formatTime(item?.created_at);
    return (
      <TouchableOpacity
        style={[styles.row, unread ? styles.rowUnread : null]}
        activeOpacity={0.85}
        onPress={() => onPressItem(item.id)}
      >
        <View style={styles.avatar}>
          <Feather name="bell" size={18} color="#fff" />
        </View>
        <View style={{ flex: 1 }}>
          <View style={styles.rowTop}>
            <Text style={styles.title} numberOfLines={1}>{title}</Text>
            <View style={styles.meta}>
              {time ? <Text style={[styles.time, unread ? styles.timeUnread : null]}>{time}</Text> : null}
              {unread ? <View style={styles.unreadDot} /> : null}
            </View>
          </View>
          {body ? <Text style={styles.body} numberOfLines={3}>{body}</Text> : null}
        </View>
      </TouchableOpacity>
    );
  };

  return (
    <View style={styles.screen}>
      <SafeAreaView style={{ flex: 1, paddingTop: insets.top }} edges={['top','left','right']}>
        <View style={styles.header}>
          <View style={styles.headerLeft}>
            <Text style={styles.headerTitle}>Notifications</Text>
            <Text style={styles.headerSub}>Updates from iMpazamon</Text>
          </View>
          <TouchableOpacity style={styles.headerBtn} onPress={onMarkAll} disabled={loading}>
            <Feather name="check" size={16} color={theme.colors.text} style={{ marginRight: 8 }} />
            <Text style={styles.headerBtnText}>Mark all</Text>
          </TouchableOpacity>
        </View>

        <FlatList
          data={sections}
          keyExtractor={(it) => String(it.title)}
          renderItem={({ item: section }) => (
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>{section.title}</Text>
              <View style={styles.sectionCard}>
                {section.data.map((n) => (
                  <View key={String(n.id)}>
                    {renderItem({ item: n })}
                    <View style={styles.separator} />
                  </View>
                ))}
              </View>
            </View>
          )}
          contentContainerStyle={{ paddingBottom: theme.spacing.xxl, paddingHorizontal: theme.spacing.xl }}
          refreshControl={<RefreshControl refreshing={refreshing} onRefresh={load} tintColor={theme.colors.primary} />}
          ListEmptyComponent={
            <View style={styles.empty}>
              <Text style={styles.emptyText}>No notifications</Text>
            </View>
          }
        />
      </SafeAreaView>
    </View>
  );
}

const styles = StyleSheet.create({
  screen: { flex: 1, backgroundColor: theme.colors.background },
  header: {
    paddingVertical: theme.spacing.lg,
    paddingHorizontal: theme.spacing.xl,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  headerLeft: { flex: 1, paddingRight: 12 },
  headerTitle: { color: theme.colors.text, fontSize: 22, fontWeight: '900' },
  headerSub: { color: theme.colors.secondaryText, fontSize: 12, marginTop: 4, fontWeight: '600' },
  headerBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: theme.borderRadius.md,
    borderWidth: 1,
    borderColor: theme.colors.border,
    backgroundColor: theme.colors.surface,
  },
  headerBtnText: { color: theme.colors.text, fontSize: theme.fontSizes.sm, fontWeight: '700' },
  section: { marginBottom: theme.spacing.lg },
  sectionTitle: { color: theme.colors.secondaryText, fontSize: 12, fontWeight: '800', marginBottom: 10, marginLeft: 2 },
  sectionCard: {
    borderWidth: 1,
    borderColor: theme.colors.border,
    borderRadius: theme.borderRadius.xl,
    backgroundColor: theme.colors.surface,
    overflow: 'hidden',
  },
  separator: { height: 1, backgroundColor: theme.colors.border, marginLeft: 64 },
  row: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    paddingHorizontal: theme.spacing.lg,
    paddingVertical: theme.spacing.lg,
    backgroundColor: 'transparent',
  },
  rowUnread: { backgroundColor: 'rgba(10,126,164,0.10)' },
  avatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: theme.colors.primary,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: theme.spacing.md,
  },
  rowTop: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' },
  title: { color: theme.colors.text, fontSize: 15, fontWeight: '900', flex: 1, paddingRight: 10 },
  meta: { flexDirection: 'row', alignItems: 'center' },
  time: { color: theme.colors.secondaryText, fontSize: 12, fontWeight: '700' },
  timeUnread: { color: theme.colors.primary },
  unreadDot: { width: 8, height: 8, borderRadius: 4, backgroundColor: theme.colors.primary, marginLeft: 8 },
  body: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.sm, marginTop: 6 },
  empty: { paddingVertical: theme.spacing.xl },
  emptyText: { color: theme.colors.secondaryText, fontSize: theme.fontSizes.md, fontWeight: '600' },
});
