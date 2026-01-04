'use client';

import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { enqueueOutbox } from '@/lib/sync/sync-engine';
import { formatJalali } from '@/lib/date';
import { db } from '@/lib/sync/db';
import { useTasks } from '@/lib/sync/use-tasks';

export default function TasksPage() {
  const [title, setTitle] = useState('');
  const tasks = useTasks();

  const handleCreate = async () => {
    if (!title.trim()) {
      return;
    }

    const id = crypto.randomUUID();
    const now = new Date().toISOString();

    await db.tasks.put({
      id,
      title,
      status: 'open',
      updatedAt: now
    });

    await enqueueOutbox({
      id,
      module: 'tasks',
      action: 'task.create',
      payload: { title, status: 'open' },
      idempotencyKey: `task-${id}`
    });

    setTitle('');
  };

  return (
    <div className="space-y-4">
      <Card>
        <CardTitle>تسک جدید</CardTitle>
        <CardDescription>ثبت تسک حتی در حالت آفلاین امکان‌پذیر است.</CardDescription>
        <div className="mt-4 flex flex-col gap-3 md:flex-row md:items-center">
          <Input value={title} onChange={(event) => setTitle(event.target.value)} placeholder="عنوان تسک" />
          <Button onClick={handleCreate}>افزودن تسک</Button>
        </div>
      </Card>

      <Card>
        <CardTitle>تسک‌های اخیر</CardTitle>
        <div className="mt-4 space-y-2">
          {tasks.length === 0 && <p className="text-sm text-base-600">تسکی ثبت نشده است.</p>}
          {tasks.map((task) => (
            <div key={task.id} className="flex items-center justify-between rounded-xl border border-base-100 p-3">
              <div>
                <p className="text-sm font-semibold">{task.title}</p>
                <p className="text-xs text-base-500">{task.status}</p>
              </div>
              <span className="text-xs text-base-500">{formatJalali(task.updatedAt)}</span>
            </div>
          ))}
        </div>
      </Card>
    </div>
  );
}
