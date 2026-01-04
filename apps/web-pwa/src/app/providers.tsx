'use client';

import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { useState } from 'react';
import { SyncProvider } from '@/lib/sync/sync-provider';
import { PwaRegister } from '@/lib/pwa/pwa-register';

export function Providers({ children }: { children: React.ReactNode }) {
  const [client] = useState(() => new QueryClient());

  return (
    <QueryClientProvider client={client}>
      <SyncProvider>
        <PwaRegister />
        {children}
      </SyncProvider>
    </QueryClientProvider>
  );
}
