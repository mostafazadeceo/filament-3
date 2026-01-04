import { afterEach, describe, expect, it, vi } from 'vitest';
import { db } from './db';
import { enqueueOutbox, pushOutbox } from './sync-engine';

vi.mock('@/lib/api-client/client', () => ({
  apiFetch: vi.fn()
}));

const getApiFetch = async () => {
  const module = await import('@/lib/api-client/client');
  return module.apiFetch as ReturnType<typeof vi.fn>;
};

describe('sync-engine', () => {
  it('enqueues outbox items', async () => {
    await enqueueOutbox({
      id: 'req-1',
      module: 'pos',
      action: 'order.create',
      payload: { total: 1000 },
      idempotencyKey: 'pos-req-1'
    });

    const count = await db.outbox.count();
    expect(count).toBe(1);
  });

  afterEach(async () => {
    const apiFetch = await getApiFetch();
    apiFetch.mockReset();
    await db.outbox.clear();
  });

  it('marks items as completed after successful push', async () => {
    const apiFetch = await getApiFetch();
    apiFetch.mockResolvedValue({
      results: [{ id: 'req-2', status: 'accepted' }]
    });

    await enqueueOutbox({
      id: 'req-2',
      module: 'pos',
      action: 'order.create',
      payload: { total: 2500 },
      idempotencyKey: 'pos-req-2'
    });

    await pushOutbox();

    const item = await db.outbox.get('req-2');
    expect(item?.status).toBe('completed');
  });

  it('marks items as failed when push throws', async () => {
    const apiFetch = await getApiFetch();
    apiFetch.mockRejectedValue(new Error('network'));

    await enqueueOutbox({
      id: 'req-3',
      module: 'support',
      action: 'ticket.create',
      payload: { subject: 'Help' },
      idempotencyKey: 'support-req-3'
    });

    await expect(pushOutbox()).rejects.toThrow('network');

    const item = await db.outbox.get('req-3');
    expect(item?.status).toBe('failed');
    expect(item?.retries).toBe(1);
  });
});
