import React, { createContext, useState, useEffect } from 'react';
import { Platform } from 'react-native';
import Constants from 'expo-constants';
import { getStoredUser, storeUser, clearUser, getStoredToken, storeToken } from '../services/auth';
import { setAuthToken, registerPushToken } from '../services/api';

export const UserContext = createContext();

export const UserProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);

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

  const logout = () => {
    setUser(null);
    setToken(null);
    setAuthToken(null);
    clearUser();
  };

  useEffect(() => {
    const projectId = Constants?.expoConfig?.extra?.eas?.projectId || Constants?.manifest?.extra?.eas?.projectId;
    const ensurePush = async () => {
      if (!user || !token || !projectId) return;
      const isExpoGo = Constants?.executionEnvironment
        ? Constants.executionEnvironment === 'storeClient'
        : Constants?.appOwnership === 'expo';
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
        const expoToken = await Notifications.getExpoPushTokenAsync({ projectId });
        const value = expoToken?.data;
        if (!value) return;
        await registerPushToken({ token: value, platform: Platform.OS });
      } catch (e) {
      }
    };
    ensurePush();
  }, [user, token]);

  return (
    <UserContext.Provider value={{ user, token, login, logout }}>
      {children}
    </UserContext.Provider>
  );
};
