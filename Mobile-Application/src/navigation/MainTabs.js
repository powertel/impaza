import React, { useEffect, useState } from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { AntDesign, Feather } from '@expo/vector-icons';
import { usePermissions } from '../hooks/usePermissions';

import { theme } from '../styles/theme';
import DashboardScreen from '../screens/DashboardScreen';
import FaultsListScreen from '../screens/FaultsListScreen';
import ProfileScreen from '../screens/ProfileScreen';
import NotificationsScreen from '../screens/NotificationsScreen';
import { getUnreadCount } from '../services/api';

import AssessmentsScreen from '../screens/AssessmentsScreen';

const Tab = createBottomTabNavigator();

export default function MainTabs() {
  const { hasPermission, hasAnyPermission, hasRole } = usePermissions();
  const [unread, setUnread] = useState(0);

  useEffect(() => {
    let mounted = true;
    const load = async () => {
      try {
        const res = await getUnreadCount();
        const value = parseInt(res?.unread || 0, 10) || 0;
        if (mounted) setUnread(value);
      } catch (e) {
      }
    };
    load();
    const id = setInterval(load, 20000);
    return () => {
      mounted = false;
      clearInterval(id);
    };
  }, []);
  
  // Determine visibility of tabs
  const showMyFaults = hasAnyPermission([
    'fault-list', 
    'my-fault-list', 
    'assigned-fault-list', 
    'noc-clear-faults-list', 
    'chief-tech-clear-faults-list'
  ]) || hasRole('Technician') || hasRole('Noc Supervisor') || hasRole('Chief Technician') || hasRole('NOC');

  const showAssess = hasPermission('fault-assessment') || hasRole('NOC') || hasRole('Noc Supervisor');

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarShowLabel: true,
        tabBarActiveTintColor: theme.colors.primary,
        tabBarInactiveTintColor: theme.colors.secondaryText,
        tabBarBadge: route.name === 'Notifications' && unread > 0 ? unread : undefined,
        tabBarBadgeStyle: { backgroundColor: theme.colors.danger, color: theme.colors.white },
        tabBarStyle: { 
          height: 64, 
          paddingBottom: 10,
          backgroundColor: theme.colors.background,
          borderTopColor: theme.colors.border,
          borderTopWidth: 1,
        },
        tabBarIcon: ({ color, size }) => {
          if (route.name === 'Home') return <AntDesign name="home" size={24} color={color} />;
          if (route.name === 'Faults') return <Feather name="alert-triangle" size={22} color={color} />;
          if (route.name === 'Assess') return <Feather name="check-square" size={22} color={color} />;
          if (route.name === 'Notifications') return <Feather name="bell" size={22} color={color} />;
          return <AntDesign name="user" size={24} color={color} />;
        },
        tabBarLabelStyle: {
          fontSize: 12,
          fontWeight: '500',
          marginTop: -4,
        }
      })}
    >
      <Tab.Screen name="Home" component={DashboardScreen} />
      {showMyFaults && (
        <Tab.Screen name="Faults" component={FaultsListScreen} />
      )}
      {showAssess && (
        <Tab.Screen name="Assess" component={AssessmentsScreen} />
      )}
      <Tab.Screen name="Notifications" component={NotificationsScreen} />
      <Tab.Screen name="Profile" component={ProfileScreen} />
    </Tab.Navigator>
  );
}
