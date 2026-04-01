import React from 'react';
import AppNavigator from './src/navigation/AppNavigator';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { StatusBar } from 'expo-status-bar';
import { UserProvider } from './src/context/UserContext';

export default function App() {
  return (
    <UserProvider>
      <SafeAreaProvider>
        <StatusBar style="dark" backgroundColor="#F5F7FF" />
        <AppNavigator />
      </SafeAreaProvider>
    </UserProvider>
  );
}
