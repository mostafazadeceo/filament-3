'use client';

import { useEffect, useState } from 'react';
import { db } from '@/lib/sync/db';

export function useOutboxCount() {
  const [count, setCount] = useState(0);

  useEffect(() => {
    let timer: NodeJS.Timeout | null = null;

    const refresh = async () => {
      const value = await db.outbox.where('status').anyOf('pending', 'failed').count();
      setCount(value);
    };

    refresh();
    timer = setInterval(refresh, 10_000);

    return () => {
      if (timer) {
        clearInterval(timer);
      }
    };
  }, []);

  return count;
}
