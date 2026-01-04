'use client';

import { useEffect, useState } from 'react';
import { db, type TaskItem } from '@/lib/sync/db';

export function useTasks() {
  const [tasks, setTasks] = useState<TaskItem[]>([]);

  useEffect(() => {
    let alive = true;

    const load = async () => {
      const data = await db.tasks.orderBy('updatedAt').reverse().limit(6).toArray();
      if (alive) {
        setTasks(data);
      }
    };

    load();
    const timer = setInterval(load, 15_000);

    return () => {
      alive = false;
      clearInterval(timer);
    };
  }, []);

  return tasks;
}
