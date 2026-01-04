const TOKEN_KEY = 'hub_access_token';
const REFRESH_KEY = 'hub_refresh_token';
const TENANT_KEY = 'hub_tenant_id';

export type SessionTokens = {
  accessToken: string;
  refreshToken?: string;
  tenantId?: string | null;
};

export function saveSession(tokens: SessionTokens) {
  if (typeof window === 'undefined') {
    return;
  }
  localStorage.setItem(TOKEN_KEY, tokens.accessToken);
  if (tokens.refreshToken) {
    localStorage.setItem(REFRESH_KEY, tokens.refreshToken);
  }
  if (tokens.tenantId) {
    localStorage.setItem(TENANT_KEY, tokens.tenantId);
  }
}

export function clearSession() {
  if (typeof window === 'undefined') {
    return;
  }
  localStorage.removeItem(TOKEN_KEY);
  localStorage.removeItem(REFRESH_KEY);
  localStorage.removeItem(TENANT_KEY);
}

export function getAccessToken(): string | null {
  if (typeof window === 'undefined') {
    return null;
  }
  return localStorage.getItem(TOKEN_KEY);
}

export function getRefreshToken(): string | null {
  if (typeof window === 'undefined') {
    return null;
  }
  return localStorage.getItem(REFRESH_KEY);
}

export function getTenantId(): string | null {
  if (typeof window === 'undefined') {
    return null;
  }
  return localStorage.getItem(TENANT_KEY);
}
