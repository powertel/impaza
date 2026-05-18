import Constants from 'expo-constants';
import { Platform } from 'react-native';

// Prefer Expo config (app.json/eas.json) for API URL. Avoid using process.env
// to prevent accidental overrides with local IPs during web dev.
const CONFIG_API_URL = Constants?.expoConfig?.extra?.apiUrl || Constants?.manifest?.extra?.apiUrl;
function extractHost(value) {
  if (!value) return null;
  const normalized = String(value).trim().replace(/^[a-zA-Z]+:\/\//, '').split('/')[0];
  const host = normalized.split(':')[0];
  return host || null;
}

function getDevHost() {
  const hostUri =
    Constants?.expoConfig?.hostUri ||
    Constants?.manifest2?.extra?.expoClient?.hostUri ||
    Constants?.manifest?.debuggerHost ||
    Constants?.manifest?.hostUri;

  const host = extractHost(hostUri);
  if (!host) return null;
  if (Platform.OS === 'android' && host === 'localhost') return '10.0.2.2';
  return host;
}

function normalizeUrl(url) {
  return String(url || '').replace(/\/$/, '');
}

const DEV_API_URL = (() => {
  const host = getDevHost();
  if (host) return `http://${host}:8087/api`;
  if (Platform.OS === 'android') return 'http://10.0.2.2:8087/api';
  return 'http://localhost:8087/api';
})();

const PROD_API_URL = 'https://impazamon.powertel.co.zw/api';
const shouldUseDevApi = __DEV__ && !CONFIG_API_URL;

export const API_URL = normalizeUrl(shouldUseDevApi ? DEV_API_URL : (CONFIG_API_URL || PROD_API_URL));
if (__DEV__) console.log('API_URL', API_URL);

let authToken = null;
export function setAuthToken(token) { authToken = token; }
export function getAuthToken() { return authToken; }

async function request(path, options = {}) {
  const isForm = options && options.body && typeof options.body === 'object' && options.body instanceof FormData;
  const headers = { Accept: 'application/json', ...(authToken ? { Authorization: `Bearer ${authToken}` } : {}), ...(options.headers || {}) };
  if (!isForm) headers['Content-Type'] = 'application/json';
  const res = await fetch(`${API_URL}${path}`, { headers, ...options });
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

export async function getMyFaults(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults${query}`);
  return data;
}

export async function getFault(id) {
  const data = await request(`/mobile/faults/${id}`);
  return data;
}

export async function rectifyFault(id, payload) {
  if (payload instanceof FormData) {
    return request(`/mobile/faults/${id}/rectify`, { method: 'POST', body: payload });
  }
  return request(`/mobile/faults/${id}/rectify`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function addFaultRemark(id, payload) {
  if (payload instanceof FormData) {
    return request(`/mobile/faults/${id}/remarks`, { method: 'POST', body: payload });
  }
  return request(`/mobile/faults/${id}/remarks`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function getAssessments(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/assessments${query}`);
  return data;
}

export async function getRectifiedFaults(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/rectified${query}`);
  return data;
}

export async function getEscalations(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/escalations${query}`);
  return data;
}

export async function getResolvedFaults(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/resolved${query}`);
  return data;
}

export async function getReferredFaults(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/referred${query}`);
  return data;
}

export async function assessFault(id, payload) {
  return request(`/mobile/faults/${id}/assess`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function clearFault(id, payload) {
  return request(`/mobile/faults/${id}/clear`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function revokeFault(id, payload) {
  return request(`/mobile/faults/${id}/revoke`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function referFault(id, payload) {
  return request(`/mobile/faults/${id}/refer`, { method: 'POST', body: JSON.stringify(payload) });
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

export async function escalateFault(id, payload) {
  return request(`/mobile/faults/${id}/escalate`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function getUnassignedFaults(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/unassigned${query}`);
  return data;
}

export async function getAssignedFaults(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/assigned${query}`);
  return data;
}

export async function getSectionFaults(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  const data = await request(`/mobile/faults/section${query}`);
  return data;
}

export async function getAssignableTechnicians() {
  return request('/mobile/technicians/assignable');
}

export async function assignFault(payload) {
  return request('/mobile/faults/assign', { method: 'POST', body: JSON.stringify(payload) });
}

export async function reassignFault(id, payload) {
  return request(`/mobile/faults/${id}/reassign`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function reassignReferral(id, payload) {
  return request(`/mobile/faults/${id}/reassign-referral`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function completeReferral(id, payload) {
  return request(`/mobile/faults/${id}/complete-referral`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function registerPushToken(payload) {
  return request('/mobile/push-tokens', { method: 'POST', body: JSON.stringify(payload) });
}

export async function unregisterPushToken(payload) {
  return request('/mobile/push-tokens/unregister', { method: 'POST', body: JSON.stringify(payload) });
}

export async function getPushTokenStatus() {
  return request('/mobile/push-tokens/status');
}

export async function testPushNotification(payload = {}) {
  return request('/mobile/push-notifications/test', { method: 'POST', body: JSON.stringify(payload) });
}

export async function getUnreadCount() {
  return request('/mobile/notifications/unread-count');
}

export async function getNotifications(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  return request(`/mobile/notifications${query}`);
}

export async function markNotificationRead(id) {
  return request(`/mobile/notifications/${id}/read`, { method: 'POST' });
}

export async function markAllNotificationsRead() {
  return request('/mobile/notifications/mark-all-read', { method: 'POST' });
}

export async function getLteSiteSurveys(params = {}) {
  const query = Object.keys(params).length ? `?${new URLSearchParams(params).toString()}` : '';
  return request(`/mobile/lte-site-surveys${query}`);
}

export async function getLteSiteSurvey(id) {
  return request(`/mobile/lte-site-surveys/${id}`);
}

export async function createLteSiteSurvey(payload) {
  if (payload instanceof FormData) {
    return request('/mobile/lte-site-surveys', { method: 'POST', body: payload });
  }
  return request('/mobile/lte-site-surveys', { method: 'POST', body: JSON.stringify(payload) });
}

export async function updateLteSiteSurvey(id, payload) {
  if (payload instanceof FormData) {
    payload.append('_method', 'PUT');
    return request(`/mobile/lte-site-surveys/${id}`, { method: 'POST', body: payload });
  }
  return request(`/mobile/lte-site-surveys/${id}`, { method: 'PUT', body: JSON.stringify(payload) });
}

export async function getLteEnabledUsers() {
  return request('/mobile/lte-site-surveys-enabled-users');
}

export async function addLteSurveyRemark(id, payload) {
  if (payload instanceof FormData) {
    return request(`/mobile/lte-site-surveys/${id}/remarks`, { method: 'POST', body: payload });
  }
  return request(`/mobile/lte-site-surveys/${id}/remarks`, { method: 'POST', body: JSON.stringify(payload) });
}

export async function deleteLteSurveyPhoto(photoId) {
  return request(`/mobile/lte-site-survey-photos/${photoId}`, { method: 'DELETE' });
}

export function lteSurveyPhotoUrl(photoId) {
  return `${API_URL}/mobile/lte-site-survey-photos/${photoId}`;
}

export function lteSurveyRemarkFileUrl(remarkId) {
  return `${API_URL}/mobile/lte-site-survey-remarks/${remarkId}/file`;
}
