'use client';

import { useEffect } from 'react';

export function PwaRegister() {
  useEffect(() => {
    if (!('serviceWorker' in navigator)) {
      return;
    }

    navigator.serviceWorker
      .register('/sw.js')
      .then(async (registration) => {
        const syncManager = (registration as ServiceWorkerRegistration & {
          sync?: { register: (tag: string) => Promise<void> };
        }).sync;
        if (!syncManager) {
          return;
        }
        try {
          await syncManager.register('app-sync');
        } catch {
          // Background sync not available; fallback handled in SyncProvider.
        }
      })
      .catch(() => {
        // Ignore SW registration errors in dev environments.
      });
  }, []);

  return null;
}
