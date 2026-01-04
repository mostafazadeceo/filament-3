import { apiFetch } from '@/lib/api-client/client';
import { clearSession, getRefreshToken, saveSession } from '@/lib/auth/session-store';

export type LoginPayload = {
  email: string;
  password: string;
  tenant_id?: string | null;
};

export type AuthResponse = {
  access_token: string;
  refresh_token?: string;
  user: {
    id: number;
    name: string;
    email: string;
  };
  tenant?: {
    id: number;
    name: string;
  } | null;
};

export async function login(payload: LoginPayload) {
  const data = await apiFetch<AuthResponse>('/api/v1/app/auth/login', {
    method: 'POST',
    body: JSON.stringify(payload)
  });
  saveSession({
    accessToken: data.access_token,
    refreshToken: data.refresh_token,
    tenantId: data.tenant?.id?.toString() ?? null
  });
  return data;
}

export async function refresh() {
  const refreshToken = getRefreshToken();
  if (!refreshToken) {
    throw new Error('refresh token missing');
  }
  const data = await apiFetch<AuthResponse>('/api/v1/app/auth/refresh', {
    method: 'POST',
    body: JSON.stringify({ refresh_token: refreshToken })
  });
  saveSession({
    accessToken: data.access_token,
    refreshToken: data.refresh_token,
    tenantId: data.tenant?.id?.toString() ?? null
  });
  return data;
}

export async function logout() {
  await apiFetch('/api/v1/app/auth/logout', { method: 'POST' });
  clearSession();
}

export async function me() {
  return apiFetch<AuthResponse['user']>('/api/v1/app/auth/me');
}
