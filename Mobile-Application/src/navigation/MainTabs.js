import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { AntDesign, Feather } from '@expo/vector-icons';
import { usePermissions } from '../hooks/usePermissions';

import DashboardScreen from '../screens/DashboardScreen';
import FaultsListScreen from '../screens/FaultsListScreen';
import ProfileScreen from '../screens/ProfileScreen';

const Tab = createBottomTabNavigator();

export default function MainTabs() {
  const { hasPermission, hasAnyPermission, hasRole } = usePermissions();
  
  // Determine if the user should see the "My Faults" tab
  // This logic can be expanded based on specific roles or permissions
  const showMyFaults = hasAnyPermission([
    'fault-list', 
    'my-fault-list', 
    'assigned-fault-list', 
    'noc-clear-faults-list', 
    'chief-tech-clear-faults-list'
  ]) || hasRole('Technician') || hasRole('Noc Supervisor') || hasRole('Chief Technician') || hasRole('NOC');

  return (
    <Tab.Navigator
      screenOptions={({ route }) => ({
        headerShown: false,
        tabBarShowLabel: true,
        tabBarActiveTintColor: '#0A66CC',
        tabBarStyle: { height: 64, paddingBottom: 8 },
        tabBarIcon: ({ color, size }) => {
          if (route.name === 'Dashboard') return <AntDesign name="home" size={size} color={color} />;
          if (route.name === 'My Faults') return <Feather name="list" size={size} color={color} />;
          return <AntDesign name="user" size={size} color={color} />;
        },
      })}
    >
      <Tab.Screen name="Dashboard" component={DashboardScreen} />
      {showMyFaults && (
        <Tab.Screen name="My Faults" component={FaultsListScreen} />
      )}
      <Tab.Screen name="Profile" component={ProfileScreen} />
    </Tab.Navigator>
  );
}