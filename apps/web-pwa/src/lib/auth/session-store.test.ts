import { beforeEach, describe, expect, it } from 'vitest';
import { clearSession, getAccessToken, getRefreshToken, getTenantId, saveSession } from './session-store';

describe('session-store', () => {
  beforeEach(() => {
    clearSession();
  });

  it('stores and reads tokens', () => {
    saveSession({ accessToken: 'token', refreshToken: 'refresh', tenantId: '1' });
    expect(getAccessToken()).toBe('token');
    expect(getRefreshToken()).toBe('refresh');
    expect(getTenantId()).toBe('1');
  });

  it('clears tokens', () => {
    saveSession({ accessToken: 'token', refreshToken: 'refresh', tenantId: '1' });
    clearSession();
    expect(getAccessToken()).toBeNull();
  });
});
