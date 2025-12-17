import React from 'react';
import { View, Text, StyleSheet } from 'react-native';
import { theme } from '../styles/theme';

export default function PasswordStrengthMeter({ password }) {
  if (!password) return null;

  const getStrength = (pw) => {
    let score = 0;
    if (pw.length >= 8) score += 1;
    if (/[a-z]/.test(pw)) score += 1;
    if (/[A-Z]/.test(pw)) score += 1;
    if (/\d/.test(pw)) score += 1;
    if (/[^A-Za-z0-9]/.test(pw)) score += 1;
    return score;
  };

  const score = getStrength(password);
  
  let strengthLabel = 'Weak';
  let strengthColor = theme.colors.danger;
  let filledBars = 1;

  // Logic: 
  // If length < 8, it's always weak/short.
  // If length >= 8, we check other criteria.
  
  if (password.length < 8) {
    strengthLabel = 'Too Short';
    strengthColor = theme.colors.danger;
    filledBars = 1;
  } else {
    if (score === 5) {
      strengthLabel = 'Strong';
      strengthColor = theme.colors.success;
      filledBars = 3;
    } else if (score >= 3) {
      strengthLabel = 'Medium';
      strengthColor = theme.colors.warning;
      filledBars = 2;
    } else {
      strengthLabel = 'Weak';
      strengthColor = theme.colors.danger;
      filledBars = 1;
    }
  }

  return (
    <View style={styles.container}>
      <View style={styles.bars}>
        {[1, 2, 3].map((index) => (
          <View
            key={index}
            style={[
              styles.bar,
              {
                backgroundColor: index <= filledBars ? strengthColor : theme.colors.lightGray,
              }
            ]}
          />
        ))}
      </View>
      <Text style={[styles.label, { color: strengthColor }]}>{strengthLabel}</Text>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    marginTop: 8,
    width: '100%',
  },
  bars: {
    flexDirection: 'row',
    height: 4,
    marginBottom: 4,
    justifyContent: 'space-between',
  },
  bar: {
    flex: 1,
    borderRadius: 2,
    marginHorizontal: 2,
  },
  label: {
    fontSize: 12,
    textAlign: 'right',
    fontWeight: '600',
  },
});
