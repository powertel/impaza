import React, { createContext, useState, useEffect } from 'react';
import { Platform } from 'react-native';
import Constants from 'expo-constants';
import { getStoredUser, storeUser, clearUser, getStoredToken, storeToken, getStoredPushToken, storePushToken, clearPushToken } from '../services/auth';
import { setAuthToken, registerPushToken, unregisterPushToken } from '../services/api';

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [isHydrated, setIsHydrated] = useState(false);

  useEffect(() => {
    const loadUser = async () => {
      const storedUser = await getStoredUser();
      const storedToken = await getStoredToken();
      if (storedUser) {
        setUser(storedUser);
      }
      if (storedToken) {
        setToken(storedToken);
        setAuthToken(storedToken);
      }
      setIsHydrated(true);
    };
    loadUser();
  }, []);

  const login = (userData, authToken) => {
    setUser(userData);
    storeUser(userData);
    if (authToken) {
      setToken(authToken);
      storeToken(authToken);
      setAuthToken(authToken);
    }
  };

  const logout = async () => {
    try {
      const pushToken = await getStoredPushToken();
      if (pushToken && token) {
        await unregisterPushToken({ token: pushToken });
      }
    } catch (e) {
    }
    setUser(null);
    setToken(null);
    setAuthToken(null);
    try { await clearPushToken(); } catch (e) {}
    try { await clearUser(); } catch (e) {}
  };

  useEffect(() => {
    const projectId = Constants?.expoConfig?.extra?.eas?.projectId || Constants?.manifest?.extra?.eas?.projectId;
    const ensurePush = async () => {
      if (!user || !token) return;
      const isExpoGo = Constants?.executionEnvironment === 'storeClient';
      if (isExpoGo) return;
      try {
        const Notifications = await import('expo-notifications');
        Notifications.setNotificationHandler({
          handleNotification: async () => ({
            shouldShowAlert: true,
            shouldPlaySound: true,
            shouldSetBadge: false,
          }),
        });
        if (Platform.OS === 'android') {
          await Notifications.setNotificationChannelAsync('default', {
            name: 'default',
            importance: Notifications.AndroidImportance.MAX,
            vibrationPattern: [0, 250, 250, 250],
            lightColor: '#FF231F7C',
          });
        }
        const perms = await Notifications.getPermissionsAsync();
        let status = perms?.status;
        if (status !== 'granted') {
          const req = await Notifications.requestPermissionsAsync();
          status = req?.status;
        }
        if (status !== 'granted') return;
        let value = null;
        try {
          if (projectId) {
            const expoToken = await Notifications.getExpoPushTokenAsync({ projectId });
            value = expoToken?.data || null;
          } else {
            const expoToken = await Notifications.getExpoPushTokenAsync();
            value = expoToken?.data || null;
          }
        } catch (e) {
          try {
            const expoToken = await Notifications.getExpoPushTokenAsync();
            value = expoToken?.data || null;
          } catch (e2) {
            value = null;
          }
        }
        if (!value) return;
        const res = await registerPushToken({ token: value, platform: Platform.OS });
        storePushToken(value);
        if (__DEV__) console.log('push-token-registered', !!res?.success);
      } catch (e) {
      }
    };
    ensurePush();
  }, [user, token]);

  return (
    <UserContext.Provider value={{ user, token, isHydrated, login, logout }}>
      {children}
    </UserContext.Provider>
  );
};
