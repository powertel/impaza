import Constants from 'expo-constants';

// Prefer Expo config (app.json/eas.json) for API URL. Avoid using process.env
// to prevent accidental overrides with local IPs during web dev.
const CONFIG_API_URL = Constants?.expoConfig?.extra?.apiUrl || Constants?.manifest?.extra?.apiUrl;
const API_ORIGIN = (CONFIG_API_URL || 'http://localhost:8087').replace(/\/$/, '');
const API_URL = `${API_ORIGIN}`;
if (__DEV__) console.log('API_URL', API_URL);

let authToken = null;
export function setAuthToken(token) { authToken = token; }

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
