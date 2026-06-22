import React from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';

import { theme } from '../styles/theme';
import SplashScreen from '../screens/SplashScreen';
import SignInScreen from '../screens/SignInScreen';
import SignUpScreen from '../screens/SignUpScreen';
import ForgotPasswordScreen from '../screens/ForgotPasswordScreen';
import FaultDetailScreen from '../screens/FaultDetailScreen';
import RectifyFaultScreen from '../screens/RectifyFaultScreen';
import AddRemarkScreen from '../screens/AddRemarkScreen';
import EscalateFaultScreen from '../screens/EscalateFaultScreen';
import MainTabs from './MainTabs';
import UnassignedFaultsScreen from '../screens/UnassignedFaultsScreen';
import AssignedFaultsScreen from '../screens/AssignedFaultsScreen';
import SectionFaultsScreen from '../screens/SectionFaultsScreen';
import AssignFaultScreen from '../screens/AssignFaultScreen';
import AssessmentsScreen from '../screens/AssessmentsScreen';
import RectifiedFaultsScreen from '../screens/RectifiedFaultsScreen';
import EscalationsScreen from '../screens/EscalationsScreen';
import ResolvedFaultsScreen from '../screens/ResolvedFaultsScreen';
import ReferredFaultsScreen from '../screens/ReferredFaultsScreen';
import AssessFaultScreen from '../screens/AssessFaultScreen';
import ClearFaultScreen from '../screens/ClearFaultScreen';
import RevokeFaultScreen from '../screens/RevokeFaultScreen';
import CompleteReferralScreen from '../screens/CompleteReferralScreen';
import LteSiteSurveysScreen from '../screens/LteSiteSurveysScreen';
import LteSiteSurveyWizardScreen from '../screens/LteSiteSurveyWizardScreen';
import LteSiteSurveyViewScreen from '../screens/LteSiteSurveyViewScreen';
import CustomerConnectivitySurveysScreen from '../screens/CustomerConnectivitySurveysScreen';
import CustomerConnectivitySurveyWizardScreen from '../screens/CustomerConnectivitySurveyWizardScreen';
import CustomerConnectivitySurveyViewScreen from '../screens/CustomerConnectivitySurveyViewScreen';

const Stack = createNativeStackNavigator();

export default function AppNavigator() {
  return (
    <NavigationContainer>
      <Stack.Navigator
        initialRouteName="Splash"
        screenOptions={{
          headerStyle: { backgroundColor: theme.colors.background },
          headerTintColor: theme.colors.text,
          headerShadowVisible: false,
          headerTitleStyle: { color: theme.colors.text },
          contentStyle: { backgroundColor: theme.colors.background },
        }}
      >
        <Stack.Screen name="Splash" component={SplashScreen} options={{ headerShown: false }} />
        <Stack.Screen name="SignIn" component={SignInScreen} options={{ headerShown: false }} />
        <Stack.Screen name="SignUp" component={SignUpScreen} options={{ title: 'Create Account' }} />
        <Stack.Screen name="ForgotPassword" component={ForgotPasswordScreen} options={{ title: 'Forgot Password' }} />
        <Stack.Screen name="Main" component={MainTabs} options={{ headerShown: false }} />
        <Stack.Screen name="FaultDetail" component={FaultDetailScreen} options={{ headerShown: false }} />
        <Stack.Screen name="RectifyFault" component={RectifyFaultScreen} options={{ headerShown: false }} />
        <Stack.Screen name="EscalateFault" component={EscalateFaultScreen} options={{ title: 'Escalate Fault' }} />
        <Stack.Screen name="AddRemark" component={AddRemarkScreen} options={{ title: 'Add Remark' }} />
        <Stack.Screen name="UnassignedFaults" component={UnassignedFaultsScreen} options={{ title: 'Unassigned Faults' }} />
        <Stack.Screen name="AssignedFaults" component={AssignedFaultsScreen} options={{ title: 'Assigned Faults' }} />
        <Stack.Screen name="SectionFaults" component={SectionFaultsScreen} options={{ title: 'Section Faults' }} />
        <Stack.Screen name="AssignFault" component={AssignFaultScreen} options={{ title: 'Assign/Reassign Fault' }} />
        <Stack.Screen name="Assessments" component={AssessmentsScreen} options={{ title: 'Assessments' }} />
        <Stack.Screen name="RectifiedFaults" component={RectifiedFaultsScreen} options={{ title: 'Rectified Faults' }} />
        <Stack.Screen name="Escalations" component={EscalationsScreen} options={{ title: 'Escalations' }} />
        <Stack.Screen name="ResolvedFaults" component={ResolvedFaultsScreen} options={{ title: 'Resolved Faults' }} />
        <Stack.Screen name="ReferredFaults" component={ReferredFaultsScreen} options={{ title: 'Referred Faults' }} />
        <Stack.Screen name="AssessFault" component={AssessFaultScreen} options={{ title: 'Assess Fault' }} />
        <Stack.Screen name="ClearFault" component={ClearFaultScreen} options={{ title: 'Clear Fault (NOC)' }} />
        <Stack.Screen name="RevokeFault" component={RevokeFaultScreen} options={{ title: 'Revoke Fault' }} />
        <Stack.Screen name="CompleteReferral" component={CompleteReferralScreen} options={{ title: 'Complete Referral' }} />
        <Stack.Screen name="LteSiteSurveys" component={LteSiteSurveysScreen} options={{ title: 'LTE Site Surveys' }} />
        <Stack.Screen name="LteSiteSurveyView" component={LteSiteSurveyViewScreen} options={{ title: 'LTE Survey' }} />
        <Stack.Screen name="LteSiteSurveyWizard" component={LteSiteSurveyWizardScreen} options={{ title: 'LTE Site Survey' }} />
        <Stack.Screen name="CustomerConnectivitySurveys" component={CustomerConnectivitySurveysScreen} options={{ title: 'Connectivity Surveys' }} />
        <Stack.Screen name="CustomerConnectivitySurveyView" component={CustomerConnectivitySurveyViewScreen} options={{ title: 'Connectivity Survey' }} />
        <Stack.Screen name="CustomerConnectivitySurveyWizard" component={CustomerConnectivitySurveyWizardScreen} options={{ title: 'Connectivity Survey' }} />
      </Stack.Navigator>
    </NavigationContainer>
  );
}
