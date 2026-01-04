'use client';

import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { enqueueOutbox } from '@/lib/sync/sync-engine';
import { db } from '@/lib/sync/db';

export default function HrPage() {
  const handleClock = async (type: 'in' | 'out') => {
    const id = crypto.randomUUID();
    const now = new Date().toISOString();

    await db.attendance.put({
      id,
      status: type === 'in' ? 'checked_in' : 'checked_out',
      clockedAt: now,
      updatedAt: now
    });

    await enqueueOutbox({
      id,
      module: 'attendance',
      action: type === 'in' ? 'checkin' : 'checkout',
      payload: { clocked_at: now, method: 'pin' },
      idempotencyKey: `attendance-${id}`
    });
  };

  return (
    <div className="space-y-4">
      <Card>
        <CardTitle>حضور و غیاب آفلاین</CardTitle>
        <CardDescription>چک‌این/چک‌اوت بدون اینترنت ثبت و پس از آنلاین شدن ارسال می‌شود.</CardDescription>
        <div className="mt-4 flex gap-3">
          <Button onClick={() => handleClock('in')}>چک‌این</Button>
          <Button variant="secondary" onClick={() => handleClock('out')}>
            چک‌اوت
          </Button>
        </div>
      </Card>

      <Card>
        <CardTitle>قواعد حریم خصوصی</CardTitle>
        <CardDescription>
          استفاده از لوکیشن/چهره فقط با رضایت و دسترسی مجاز انجام می‌شود. هیچ پایش دائمی فعال نیست.
        </CardDescription>
      </Card>
    </div>
  );
}
