import Constants from 'expo-constants';

const API_URL = (Constants?.expoConfig?.extra?.apiUrl || process.env.EXPO_PUBLIC_API_URL || 'http://192.168.19.91:8080/api');
if (__DEV__) console.log('API_URL', API_URL);

let authToken = null;
export function setAuthToken(token) { authToken = token; }

async function request(path, options = {}) {
  const res = await fetch(`${API_URL}${path}`, {
    headers: { 'Content-Type': 'application/json', ...(authToken ? { Authorization: `Bearer ${authToken}` } : {}), ...(options.headers || {}) },
    ...options,
  });
  return res.json();
}

export async function login(email, password) {
  const data = await request('/mobile/login', { method: 'POST', body: JSON.stringify({ email, password }) });
  if (data?.token) setAuthToken(data.token);
  return data;
}

export async function register(payload) {
  return request('/mobile/register', { method: 'POST', body: JSON.stringify(payload) });
}

export async function getMyFaults() {
  const data = await request('/mobile/faults');
  return Array.isArray(data) ? data : (data?.faults || []);
}

export async function getFault(id) {
  return request(`/mobile/faults/${id}`);
}

export async function rectifyFault(id, payload) {
  return request(`/mobile/faults/${id}/rectify`, { method: 'POST', body: JSON.stringify(payload) });
}