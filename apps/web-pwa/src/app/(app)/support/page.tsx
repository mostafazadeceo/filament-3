'use client';

import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { enqueueOutbox } from '@/lib/sync/sync-engine';
import { formatJalali } from '@/lib/date';
import { db } from '@/lib/sync/db';
import { useSupportTickets } from '@/lib/sync/use-support-tickets';
import { useState } from 'react';

export default function SupportPage() {
  const [subject, setSubject] = useState('');
  const tickets = useSupportTickets();

  const handleCreate = async () => {
    if (!subject.trim()) {
      return;
    }

    const id = crypto.randomUUID();
    const now = new Date().toISOString();

    await db.supportTickets.put({
      id,
      subject,
      status: 'open',
      priority: 'normal',
      updatedAt: now
    });

    await enqueueOutbox({
      id,
      module: 'support',
      action: 'ticket.create',
      payload: { subject, priority: 'normal' },
      idempotencyKey: `support-${id}`
    });

    setSubject('');
  };

  return (
    <div className="space-y-4">
      <Card>
        <CardTitle>تیکت جدید</CardTitle>
        <CardDescription>تیکت به‌صورت آفلاین ثبت و بعداً همگام می‌شود.</CardDescription>
        <div className="mt-4 flex flex-col gap-3 md:flex-row md:items-center">
          <Input value={subject} onChange={(event) => setSubject(event.target.value)} placeholder="موضوع تیکت" />
          <Button onClick={handleCreate}>ثبت تیکت</Button>
        </div>
      </Card>

      <Card>
        <CardTitle>تیکت‌های اخیر</CardTitle>
        <div className="mt-4 space-y-2">
          {tickets.length === 0 && <p className="text-sm text-base-600">هیچ تیکتی ثبت نشده است.</p>}
          {tickets.map((ticket) => (
            <div key={ticket.id} className="flex items-center justify-between rounded-xl border border-base-100 p-3">
              <div>
                <p className="text-sm font-semibold">{ticket.subject}</p>
                <p className="text-xs text-base-500">{ticket.status} • {ticket.priority}</p>
              </div>
              <span className="text-xs text-base-500">{formatJalali(ticket.updatedAt)}</span>
            </div>
          ))}
        </div>
      </Card>
    </div>
  );
}
