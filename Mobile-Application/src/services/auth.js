import AsyncStorage from '@react-native-async-storage/async-storage';

const USER_KEY = 'user';

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

export async function clearUser() {
  try {
    await AsyncStorage.removeItem(USER_KEY);
  } catch (e) {
    console.error('Failed to clear user from storage', e);
  }
}