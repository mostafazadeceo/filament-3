'use client';

import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { useOutboxCount } from '@/lib/sync/use-outbox-count';
import { enqueueOutbox } from '@/lib/sync/sync-engine';
import { db } from '@/lib/sync/db';
import { usePosOrders } from '@/lib/sync/use-pos-orders';

export default function PosPage() {
  const outboxCount = useOutboxCount();
  const orders = usePosOrders();

  const handleOfflineOrder = async () => {
    const id = crypto.randomUUID();
    const now = new Date().toISOString();

    await db.posOrders.put({
      id,
      status: 'draft',
      total: 0,
      payment_status: 'pending',
      updatedAt: now,
      offline: true
    });

    await enqueueOutbox({
      id,
      module: 'pos',
      action: 'order.create',
      payload: {
        status: 'draft',
        total: 0,
        payment_status: 'pending',
        offline: true
      },
      idempotencyKey: `pos-${id}`
    });
  };

  return (
    <div className="space-y-4">
      <Card>
        <div className="flex items-center justify-between">
          <div>
            <CardTitle>POS آفلاین</CardTitle>
            <CardDescription>
              سفارش آفلاین ثبت می‌شود و بعد از آنلاین شدن همگام می‌گردد. صف فعلی: {outboxCount}
            </CardDescription>
          </div>
          <Button onClick={handleOfflineOrder}>ثبت سفارش آفلاین</Button>
        </div>
      </Card>

      <Card>
        <CardTitle>سفارش‌های اخیر</CardTitle>
        <div className="mt-4 space-y-2">
          {orders.length === 0 && <p className="text-sm text-base-600">فعلاً سفارشی ثبت نشده است.</p>}
          {orders.map((order) => (
            <div key={order.id} className="flex items-center justify-between rounded-xl border border-base-100 p-3">
              <div>
                <p className="text-sm font-semibold">سفارش {order.id.slice(0, 6)}</p>
                <p className="text-xs text-base-500">{order.status}</p>
              </div>
              <span className="text-sm text-base-700">{order.total.toLocaleString('fa-IR')} تومان</span>
            </div>
          ))}
        </div>
      </Card>
    </div>
  );
}
