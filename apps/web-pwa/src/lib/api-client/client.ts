import { getAccessToken } from '@/lib/auth/session-store';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_BASE_URL ?? '';
const API_KEY = process.env.NEXT_PUBLIC_API_KEY ?? '';

export type ApiError = {
  message: string;
  status: number;
  details?: unknown;
};

export async function apiFetch<T>(path: string, init: RequestInit = {}): Promise<T> {
  const headers = new Headers(init.headers || {});
  if (!headers.has('Content-Type') && !(init.body instanceof FormData)) {
    headers.set('Content-Type', 'application/json');
  }
  if (API_KEY) {
    headers.set('X-Api-Key', API_KEY);
  }
  const token = getAccessToken();
  if (token) {
    headers.set('Authorization', `Bearer ${token}`);
  }

  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...init,
    headers
  });

  if (!response.ok) {
    let payload: unknown = null;
    try {
      payload = await response.json();
    } catch {
      payload = await response.text();
    }
    const error: ApiError = {
      message: (payload as { message?: string })?.message ?? 'خطای شبکه',
      status: response.status,
      details: payload
    };
    throw error;
  }

  if (response.status === 204) {
    return null as T;
  }

  return (await response.json()) as T;
}
