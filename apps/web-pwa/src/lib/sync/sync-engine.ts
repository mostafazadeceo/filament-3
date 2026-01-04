import { apiFetch } from '@/lib/api-client/client';
import { db, type OutboxItem } from '@/lib/sync/db';

export type SyncChange = {
  module: string;
  entity: string;
  id: string;
  action: 'upsert' | 'delete';
  payload: Record<string, unknown>;
  updated_at: string;
};

export type PullResponse = {
  next_cursor: string;
  changes: SyncChange[];
};

export type PushItem = {
  id: string;
  module: string;
  action: string;
  payload: unknown;
  idempotency_key: string;
};

export type PushResponse = {
  results: Array<{ id: string; status: 'accepted' | 'conflict' | 'failed'; message?: string }>;
};

export async function enqueueOutbox(item: Omit<OutboxItem, 'status' | 'retries' | 'createdAt' | 'updatedAt'>) {
  const now = new Date().toISOString();
  await db.outbox.put({
    ...item,
    status: 'pending',
    retries: 0,
    createdAt: now,
    updatedAt: now
  });
}

export async function pushOutbox() {
  const pending = await db.outbox.where('status').anyOf('pending', 'failed').toArray();
  if (!pending.length) {
    return;
  }
  await db.outbox.bulkPut(
    pending.map((item) => ({ ...item, status: 'syncing', updatedAt: new Date().toISOString() }))
  );

  const payload: PushItem[] = pending.map((item) => ({
    id: item.id,
    module: item.module,
    action: item.action,
    payload: item.payload,
    idempotency_key: item.idempotencyKey
  }));

  try {
    const result = await apiFetch<PushResponse>('/api/v1/app/sync/push', {
      method: 'POST',
      body: JSON.stringify({ items: payload })
    });

    const processed = new Set<string>();
    const now = new Date().toISOString();
    for (const entry of result.results) {
      const item = pending.find((p) => p.id === entry.id);
      if (!item) {
        continue;
      }
      processed.add(entry.id);
      const status = entry.status === 'accepted' ? 'completed' : 'failed';
      const retries = status === 'failed' ? item.retries + 1 : item.retries;
      await db.outbox.put({ ...item, status, retries, updatedAt: now });
    }

    for (const item of pending) {
      if (processed.has(item.id)) {
        continue;
      }
      await db.outbox.put({
        ...item,
        status: 'failed',
        retries: item.retries + 1,
        updatedAt: now
      });
    }
  } catch (error) {
    const now = new Date().toISOString();
    await db.outbox.bulkPut(
      pending.map((item) => ({
        ...item,
        status: 'failed',
        retries: item.retries + 1,
        updatedAt: now
      }))
    );
    throw error;
  }
}

export async function pullChanges() {
  const cursor = await db.cursors.get('global');
  const response = await apiFetch<PullResponse>(`/api/v1/app/sync/pull?cursor=${cursor?.cursor ?? ''}`);
  await applyChanges(response.changes);
  await db.cursors.put({ module: 'global', cursor: response.next_cursor, updatedAt: new Date().toISOString() });
}

async function applyChanges(changes: SyncChange[]) {
  for (const change of changes) {
    if (change.module === 'pos' && change.entity === 'order') {
      if (change.action === 'delete') {
        await db.posOrders.delete(change.id);
      } else {
        await db.posOrders.put({
          id: change.id,
          status: String(change.payload.status ?? 'pending'),
          total: Number(change.payload.total ?? 0),
          payment_status: change.payload.payment_status as string | undefined,
          offline: Boolean(change.payload.offline ?? false),
          updatedAt: change.updated_at
        });
      }
      continue;
    }

    if (change.module === 'support' && change.entity === 'ticket') {
      if (change.action === 'delete') {
        await db.supportTickets.delete(change.id);
      } else {
        await db.supportTickets.put({
          id: change.id,
          subject: String(change.payload.subject ?? ''),
          status: String(change.payload.status ?? 'open'),
          priority: String(change.payload.priority ?? 'normal'),
          updatedAt: change.updated_at
        });
      }
      continue;
    }

    if (change.module === 'tasks' && change.entity === 'task') {
      if (change.action === 'delete') {
        await db.tasks.delete(change.id);
      } else {
        await db.tasks.put({
          id: change.id,
          title: String(change.payload.title ?? ''),
          status: String(change.payload.status ?? 'open'),
          updatedAt: change.updated_at
        });
      }
      continue;
    }

    if (change.module === 'attendance' && change.entity === 'record') {
      if (change.action === 'delete') {
        await db.attendance.delete(change.id);
      } else {
        await db.attendance.put({
          id: change.id,
          status: String(change.payload.status ?? 'clocked'),
          clockedAt: String(change.payload.clocked_at ?? ''),
          updatedAt: change.updated_at
        });
      }
      continue;
    }

    if (change.module === 'meetings' && change.entity === 'meeting') {
      if (change.action === 'delete') {
        await db.meetings.delete(change.id);
      } else {
        await db.meetings.put({
          id: change.id,
          title: String(change.payload.title ?? ''),
          scheduledAt: change.payload.scheduled_at as string | undefined,
          updatedAt: change.updated_at
        });
      }
      continue;
    }

    if (change.module === 'crypto' && change.entity === 'invoice') {
      if (change.action === 'delete') {
        await db.cryptoInvoices.delete(change.id);
      } else {
        await db.cryptoInvoices.put({
          id: change.id,
          status: String(change.payload.status ?? 'pending'),
          amount: Number(change.payload.amount ?? 0),
          updatedAt: change.updated_at
        });
      }
      continue;
    }

    if (change.module === 'loyalty' && change.entity === 'profile') {
      if (change.action === 'delete') {
        await db.loyaltyProfiles.delete(change.id);
      } else {
        await db.loyaltyProfiles.put({
          id: change.id,
          name: String(change.payload.name ?? ''),
          tier: change.payload.tier as string | undefined,
          points: Number(change.payload.points ?? 0),
          updatedAt: change.updated_at
        });
      }
    }
  }
}
