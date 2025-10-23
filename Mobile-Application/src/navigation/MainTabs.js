import React from 'react';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { AntDesign, Feather } from '@expo/vector-icons';

import DashboardScreen from '../screens/DashboardScreen';
import FaultsListScreen from '../screens/FaultsListScreen';
import ProfileScreen from '../screens/ProfileScreen';

const Tab = createBottomTabNavigator();

export default function MainTabs() {
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
      <Tab.Screen name="My Faults" component={FaultsListScreen} />
      <Tab.Screen name="Profile" component={ProfileScreen} />
    </Tab.Navigator>
  );
}