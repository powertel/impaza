import React, { createContext, useEffect, useRef, useState } from 'react';
import { AppState, Platform } from 'react-native';
import Constants from 'expo-constants';
import { getStoredUser, storeUser, clearUser, getStoredToken, storeToken, getStoredPushToken, storePushToken, clearPushToken } from '../services/auth';
import { setAuthToken, registerPushToken, unregisterPushToken, getPushTokenStatus } from '../services/api';

export const UserContext = createContext();

const ANDROID_PUSH_CHANNEL_ID = 'impazamon_alerts';

export const UserProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [isHydrated, setIsHydrated] = useState(false);
  const appStateRef = useRef(AppState.currentState);
  const lastOriginRef = useRef(null);

  useEffect(() => {
    const sub = AppState.addEventListener('change', (next) => {
      appStateRef.current = next;
    });
    return () => {
      try { sub?.remove?.(); } catch (e) {}
    };
  }, []);

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
          handleNotification: async (notification) => {
            const data = notification?.request?.content?.data || {};
            const forceTray = data && data.__forceTray === true;
            return {
              shouldShowAlert: forceTray,
              shouldPlaySound: true,
              shouldSetBadge: false,
            };
          },
        });
        if (Platform.OS === 'android') {
          await Notifications.setNotificationChannelAsync(ANDROID_PUSH_CHANNEL_ID, {
            name: 'Alerts',
            importance: Notifications.AndroidImportance.MAX,
            vibrationPattern: [0, 250, 250, 250],
            lightColor: '#FF231F7C',
            sound: 'default',
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
        if (res?.success) {
          storePushToken(value);
        }
        if (__DEV__) {
          console.log('push-token-register-res', res);
          try {
            const status = await getPushTokenStatus();
            console.log('push-token-status', status);
          } catch (e) {
          }
        }
      } catch (e) {
      }
    };
    ensurePush();
  }, [user, token]);

  useEffect(() => {
    let subscription = null;
    const isExpoGo = Constants?.executionEnvironment === 'storeClient';
    if (isExpoGo) return () => {};

    const start = async () => {
      try {
        const Notifications = await import('expo-notifications');
        subscription = Notifications.addNotificationReceivedListener(async (notification) => {
          try {
            if (appStateRef.current !== 'active') return;
            if (Platform.OS !== 'android') return;

            const content = notification?.request?.content || {};
            const data = content?.data || {};
            if (data && data.__forceTray === true) return;

            const title = String(content?.title || 'iMpazamon');
            const body = String(content?.body || '');
            const origin = String(notification?.request?.identifier || data?.id || `${title}|${body}`);
            if (lastOriginRef.current === origin) return;
            lastOriginRef.current = origin;

            await Notifications.scheduleNotificationAsync({
              content: {
                title,
                body,
                sound: 'default',
                channelId: ANDROID_PUSH_CHANNEL_ID,
                data: { ...data, __forceTray: true, __origin: origin },
              },
              trigger: null,
            });
          } catch (e) {
          }
        });
      } catch (e) {
      }
    };

    start();
    return () => {
      try { subscription?.remove?.(); } catch (e) {}
    };
  }, []);

  return (
    <UserContext.Provider value={{ user, token, isHydrated, login, logout }}>
      {children}
    </UserContext.Provider>
  );
};
