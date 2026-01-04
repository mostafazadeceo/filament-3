'use client';

import { useEffect, useState } from 'react';
import { db, type PosOrder } from '@/lib/sync/db';

export function usePosOrders() {
  const [orders, setOrders] = useState<PosOrder[]>([]);

  useEffect(() => {
    let alive = true;

    const load = async () => {
      const data = await db.posOrders.orderBy('updatedAt').reverse().limit(5).toArray();
      if (alive) {
        setOrders(data);
      }
    };

    load();
    const timer = setInterval(load, 15_000);

    return () => {
      alive = false;
      clearInterval(timer);
    };
  }, []);

  return orders;
}
