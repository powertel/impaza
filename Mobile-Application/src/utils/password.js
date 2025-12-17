// Shared password policy utilities for the mobile app
// Policy: at least 8 chars, include lowercase, uppercase, number, and special character

export const STRONG_RE = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

export function isStrongPassword(pw) {
  return typeof pw === 'string' && STRONG_RE.test(pw);
}

export function passwordsMatch(pw, confirm) {
  return (pw || '') === (confirm || '');
}

export const POLICY_HINT = 'Minimum 8 characters; include lowercase, uppercase, number, and special character.';

