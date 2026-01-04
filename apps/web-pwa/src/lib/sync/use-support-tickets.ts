'use client';

import { useEffect, useState } from 'react';
import { db, type SupportTicket } from '@/lib/sync/db';

export function useSupportTickets() {
  const [tickets, setTickets] = useState<SupportTicket[]>([]);

  useEffect(() => {
    let alive = true;

    const load = async () => {
      const data = await db.supportTickets.orderBy('updatedAt').reverse().limit(5).toArray();
      if (alive) {
        setTickets(data);
      }
    };

    load();
    const timer = setInterval(load, 15_000);

    return () => {
      alive = false;
      clearInterval(timer);
    };
  }, []);

  return tickets;
}
