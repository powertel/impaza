import React, { useState } from 'react';
import { View, Text, TextInput, TouchableOpacity, StyleSheet } from 'react-native';
import { theme } from '../styles/theme';

export default function ForgotPasswordScreen({ navigation }) {
  const [email, setEmail] = useState('');

  const handlePasswordReset = () => {
    // TODO: Implement password reset logic
    console.log('Password reset for:', email);
    navigation.goBack();
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Forgot Password</Text>
      <View style={styles.field}>
        <TextInput
          style={styles.input}
          placeholder="Email"
          value={email}
          onChangeText={setEmail}
          keyboardType="email-address"
          autoCapitalize="none"
        />
      </View>
      <TouchableOpacity style={styles.primaryBtn} onPress={handlePasswordReset}>
        <Text style={styles.primaryBtnText}>Reset Password</Text>
      </TouchableOpacity>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.white,
    alignItems: 'center',
    justifyContent: 'center',
    padding: theme.spacing.m,
  },
  title: {
    fontSize: theme.fontSizes.xl,
    fontWeight: 'bold',
    marginBottom: theme.spacing.l,
    color: theme.colors.text,
  },
  field: {
    width: '100%',
    marginBottom: theme.spacing.m,
  },
  input: {
    backgroundColor: theme.colors.input,
    padding: theme.spacing.m,
    borderRadius: theme.spacing.xs,
    borderWidth: 1,
    borderColor: theme.colors.border,
    width: '100%',
  },
  primaryBtn: {
    backgroundColor: theme.colors.primary,
    padding: theme.spacing.m,
    borderRadius: theme.spacing.xs,
    alignItems: 'center',
    width: '100%',
  },
  primaryBtnText: {
    color: theme.colors.white,
    fontWeight: 'bold',
    fontSize: theme.fontSizes.m,
  },
});