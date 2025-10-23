import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

import SignInScreen from '../screens/SignInScreen';
import SignUpScreen from '../screens/SignUpScreen';
import FaultDetailScreen from '../screens/FaultDetailScreen';
import RectifyFaultScreen from '../screens/RectifyFaultScreen';
import MainTabs from './MainTabs';

const Stack = createNativeStackNavigator();

export default function AppNavigator() {
  return (
    <NavigationContainer>
      <Stack.Navigator initialRouteName="SignIn">
        <Stack.Screen name="SignIn" component={SignInScreen} options={{ headerShown: false }} />
        <Stack.Screen name="SignUp" component={SignUpScreen} options={{ title: 'Create Account' }} />
        <Stack.Screen name="Main" component={MainTabs} options={{ headerShown: false }} />
        <Stack.Screen name="FaultDetail" component={FaultDetailScreen} options={{ title: 'Fault Details' }} />
        <Stack.Screen name="RectifyFault" component={RectifyFaultScreen} options={{ title: 'Rectify Fault' }} />
      </Stack.Navigator>
    </NavigationContainer>
  );
}