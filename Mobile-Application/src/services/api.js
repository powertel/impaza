import Constants from 'expo-constants';

// Prefer Expo config (app.json/eas.json) for API URL. Avoid using process.env
// to prevent accidental overrides with local IPs during web dev.
const CONFIG_API_URL = Constants?.expoConfig?.extra?.apiUrl || Constants?.manifest?.extra?.apiUrl;
const API_URL = CONFIG_API_URL || 'https://impazamon.powertel.co.zw/';
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

// Profile endpoints
export async function getProfile() {
  const data = await request('/mobile/profile');
  return data?.user || null;
}

export async function updateProfile(payload) {
  return request('/mobile/profile', { method: 'PUT', body: JSON.stringify(payload) });
}

export async function changePassword(payload) {
  // payload: { newpassword, newpassword_confirmation }
  return request('/mobile/profile/password', { method: 'POST', body: JSON.stringify(payload) });
}

export async function getMyFaults() {
  const data = await request('/mobile/faults');
  return Array.isArray(data) ? data : (data?.faults || []);
}

export async function getFault(id) {
  const data = await request(`/mobile/faults/${id}`);
  return data;
}

export async function rectifyFault(id, payload) {
  return request(`/mobile/faults/${id}/rectify`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function addFaultRemark(id, payload) {
  return request(`/mobile/faults/${id}/remarks`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function getTechnicianStats(params = {}) {
  const query = Object.keys(params || {}).length ? `?${new URLSearchParams(params).toString()}` : '';
  return request(`/mobile/technician-stats${query}`);
}

export async function getRFOs() {
  return request('/mobile/rfos');
}

export async function getSections() {
  return request('/mobile/sections');
}

export async function referFault(id, payload) {
  return request(`/mobile/faults/${id}/refer`, { method: 'POST', body: JSON.stringify(payload) });
}