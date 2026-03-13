import AsyncStorage from '@react-native-async-storage/async-storage';

const USER_KEY = 'user';
const TOKEN_KEY = 'token';

export async function getStoredUser() {
  try {
    const jsonValue = await AsyncStorage.getItem(USER_KEY);
    return jsonValue != null ? JSON.parse(jsonValue) : null;
  } catch (e) {
    console.error('Failed to fetch user from storage', e);
    return null;
  }
}

export async function storeUser(user) {
  try {
    const jsonValue = JSON.stringify(user);
    await AsyncStorage.setItem(USER_KEY, jsonValue);
  } catch (e) {
    console.error('Failed to save user to storage', e);
  }
}

export async function getStoredToken() {
  try {
    return await AsyncStorage.getItem(TOKEN_KEY);
  } catch (e) {
    console.error('Failed to fetch token from storage', e);
    return null;
  }
}

export async function storeToken(token) {
  try {
    await AsyncStorage.setItem(TOKEN_KEY, String(token || ''));
  } catch (e) {
    console.error('Failed to save token to storage', e);
  }
}

export async function clearUser() {
  try {
    await AsyncStorage.removeItem(USER_KEY);
    await AsyncStorage.removeItem(TOKEN_KEY);
  } catch (e) {
    console.error('Failed to clear user from storage', e);
  }
}
