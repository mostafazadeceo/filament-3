'use client';

import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { enqueueOutbox } from '@/lib/sync/sync-engine';

export default function MeetingsPage() {
  const handleAgenda = async () => {
    const id = crypto.randomUUID();
    await enqueueOutbox({
      id,
      module: 'meetings',
      action: 'agenda.generate',
      payload: { template: 'daily-standup' },
      idempotencyKey: `meeting-agenda-${id}`
    });
  };

  const handleMinutes = async () => {
    const id = crypto.randomUUID();
    await enqueueOutbox({
      id,
      module: 'meetings',
      action: 'minutes.generate',
      payload: { template: 'standard' },
      idempotencyKey: `meeting-minutes-${id}`
    });
  };

  return (
    <div className="space-y-4">
      <Card>
        <CardTitle>جلسه امروز</CardTitle>
        <CardDescription>دستورجلسه و صورت‌جلسه با الگوهای استاندارد ایجاد می‌شوند.</CardDescription>
        <div className="mt-4 flex flex-wrap gap-3">
          <Button onClick={handleAgenda}>تولید دستورجلسه</Button>
          <Button variant="secondary" onClick={handleMinutes}>
            تولید صورت‌جلسه
          </Button>
        </div>
      </Card>

      <Card>
        <CardTitle>تماس آنلاین</CardTitle>
        <CardDescription>
          تماس ۱:۱ از طریق WebRTC فعال می‌شود؛ در صورت عدم امکان، پیام صوتی ثبت می‌گردد.
        </CardDescription>
      </Card>
    </div>
  );
}
