'use client';

import { useEffect } from 'react';
import { connectRealtime } from '@/lib/realtime/socket';
import { pullChanges, pushOutbox } from '@/lib/sync/sync-engine';

export function useRealtime() {
  useEffect(() => {
    const url = process.env.NEXT_PUBLIC_WS_URL;
    if (!url) {
      return;
    }

    const disconnect = connectRealtime({
      url,
      onEvent: () => {
        // Events are handled by feature-specific stores.
      },
      onFallback: () => {
        void pushOutbox()
          .then(() => pullChanges())
          .catch(() => {
            // Fallback errors are handled by the sync provider loop.
          });
      }
    });

    return () => disconnect();
  }, []);
}
