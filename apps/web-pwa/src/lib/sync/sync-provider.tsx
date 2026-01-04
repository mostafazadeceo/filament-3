'use client';

import { createContext, useContext, useEffect, useMemo, useState } from 'react';
import { pullChanges, pushOutbox } from '@/lib/sync/sync-engine';

type SyncState = {
  lastSyncAt?: string;
  status: 'idle' | 'syncing' | 'error';
  error?: string;
};

const SyncContext = createContext<SyncState>({ status: 'idle' });

export function SyncProvider({ children }: { children: React.ReactNode }) {
  const [state, setState] = useState<SyncState>({ status: 'idle' });

  useEffect(() => {
    let timer: NodeJS.Timeout | null = null;

    const runSync = async () => {
      setState((prev) => ({ ...prev, status: 'syncing', error: undefined }));
      try {
        await pushOutbox();
        await pullChanges();
        setState({ status: 'idle', lastSyncAt: new Date().toISOString() });
      } catch (error) {
        setState({ status: 'error', error: (error as Error).message });
      }
    };

    runSync();
    timer = setInterval(runSync, 45_000);

    const handleOnline = () => runSync();
    window.addEventListener('online', handleOnline);

    return () => {
      if (timer) {
        clearInterval(timer);
      }
      window.removeEventListener('online', handleOnline);
    };
  }, []);

  const value = useMemo(() => state, [state]);

  return <SyncContext.Provider value={value}>{children}</SyncContext.Provider>;
}

export function useSyncState() {
  return useContext(SyncContext);
}
