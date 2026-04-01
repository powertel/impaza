import React, { useContext, useEffect, useMemo, useState } from 'react';
import { View, Text, StyleSheet, StatusBar, Image } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import { useNavigation } from '@react-navigation/native';
import { LinearGradient } from 'expo-linear-gradient';
import { theme } from '../styles/theme';
import { UserContext } from '../context/UserContext';
import { getProfile, setAuthToken } from '../services/api';

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

export default function SplashScreen() {
  const navigation = useNavigation();
  const [activeDot, setActiveDot] = useState(0);
  const { user, token, isHydrated, login, logout } = useContext(UserContext);

  const dots = useMemo(() => [0, 1, 2], []);

  useEffect(() => {
    const dotTimer = setInterval(() => {
      setActiveDot((prev) => (prev + 1) % 3);
    }, 450);

    return () => {
      clearInterval(dotTimer);
    };
  }, []);

  useEffect(() => {
    if (!isHydrated) return;
    let cancelled = false;

    const go = async () => {
      await sleep(5000);
      if (cancelled) return;

      if (token) {
        try {
          setAuthToken(token);
          const profile = await getProfile();
          if (profile && profile.id) {
            login(profile, token);
            navigation.replace('Main');
            return;
          }
        } catch (e) {
        }

        await logout();
      }

      navigation.replace('SignIn');
    };

    go();
    return () => { cancelled = true; };
  }, [isHydrated, token]);

  return (
    <View style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor={theme.colors.background} />
      <LinearGradient
        colors={[theme.colors.primary, '#053d52']}
        start={{ x: 0.2, y: 0 }}
        end={{ x: 0.8, y: 1 }}
        style={styles.gradient}
      >
        <SafeAreaView style={styles.safe} edges={['top', 'left', 'right', 'bottom']}>
          <View style={styles.content}>
            <View style={styles.logoContainer}>
              <Image source={require('../../assets/impazamon-v2.png')} style={styles.logo} resizeMode="contain" />
            </View>

            {/* <Text style={styles.title}>IMPAZAMON MOBILE</Text> */}
            <Text style={styles.subtitle}>Fault Management System</Text>

            <View style={styles.dotsRow} accessibilityLabel="Loading">
              {dots.map((d) => (
                <View
                  key={d}
                  style={[
                    styles.dot,
                    { opacity: activeDot === d ? 1 : 0.35 },
                  ]}
                />
              ))}
            </View>

            <Text style={styles.loadingText}>Loading...</Text>
          </View>

          <Text style={styles.footer}>Powered by Powertel</Text>
        </SafeAreaView>
      </LinearGradient>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: theme.colors.background,
  },
  gradient: { flex: 1 },
  safe: { flex: 1 },
  content: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: theme.spacing.xl,
  },
  logoContainer: {
    width: 200,
    height: 200,
    borderRadius: theme.borderRadius.xl,
    backgroundColor: 'rgba(255,255,255,0.2)',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: theme.spacing.xl,
    borderWidth: 1,
    borderColor: 'rgba(255,255,255,0.15)',
  },
  logo: {
    width: 160,
    height: 160,
  },
  title: {
    color: theme.colors.white,
    fontSize: theme.fontSizes.xxl,
    fontWeight: '800',
    letterSpacing: 0.5,
  },
  subtitle: {
    color: 'rgba(255,255,255,0.8)',
    marginTop: 6,
    fontSize: theme.fontSizes.md,
    textAlign: 'center',
  },
  dotsRow: {
    flexDirection: 'row',
    marginTop: theme.spacing.xl,
  },
  dot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    backgroundColor: '#fff',
    marginHorizontal: 4,
  },
  loadingText: {
    color: 'rgba(255,255,255,0.8)',
    marginTop: theme.spacing.md,
    fontSize: theme.fontSizes.sm,
    fontWeight: '600',
  },
  footer: {
    textAlign: 'center',
    color: 'rgba(255,255,255,0.6)',
    fontSize: theme.fontSizes.sm,
    paddingBottom: theme.spacing.lg,
  },
});
