'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { logout } from '@/lib/auth/auth-service';
import { useCapabilities } from '@/lib/permissions/use-capabilities';
import { useSyncState } from '@/lib/sync/sync-provider';
import { useMe } from '@/lib/auth/use-me';
import { cn } from '@/lib/utils';
import { useRealtime } from '@/lib/realtime/use-realtime';

const NAV_ITEMS = [
  { href: '/', label: 'خانه', permission: null },
  { href: '/pos', label: 'POS و فروش', permission: 'pos.view' },
  { href: '/support', label: 'پشتیبانی', permission: 'support.ticket.view' },
  { href: '/hr', label: 'حضور و غیاب', permission: 'payroll.attendance.view' },
  { href: '/tasks', label: 'تسک‌ها', permission: 'workhub.work_item.view' },
  { href: '/meetings', label: 'جلسات', permission: 'meetings.view' },
  { href: '/crypto', label: 'رمزارز و کیف‌پول', permission: 'crypto.invoices.view' },
  { href: '/loyalty', label: 'باشگاه مشتریان', permission: 'loyalty.view' },
  { href: '/auditor', label: 'ممیزی هوشمند', permission: 'ai.audit.view' }
];

export function AppShell({ children }: { children: React.ReactNode }) {
  const pathname = usePathname();
  const { data: caps } = useCapabilities();
  const { data: user } = useMe();
  const sync = useSyncState();
  useRealtime();

  const permissionSet = new Set(caps?.permissions.map((item) => item.key) ?? []);

  const nav = NAV_ITEMS.filter((item) => !item.permission || permissionSet.has(item.permission));

  const handleLogout = async () => {
    await logout();
    window.location.href = '/auth/login';
  };

  return (
    <div className="min-h-screen">
      <header className="sticky top-0 z-20 border-b border-base-100/80 bg-white/70 backdrop-blur">
        <div className="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
          <div>
            <p className="text-sm text-base-600">هاب عملیاتی</p>
            <h1 className="text-lg font-semibold text-base-900">Haida Hub</h1>
          </div>
          <div className="flex items-center gap-4">
            <Badge tone={sync.status === 'error' ? 'amber' : 'teal'}>
              {sync.status === 'syncing'
                ? 'همگام‌سازی...'
                : sync.status === 'error'
                  ? 'خطای همگام‌سازی'
                  : 'همگام'}
            </Badge>
            <div className="text-right">
              <p className="text-sm text-base-700">{user?.name ?? 'کاربر'}</p>
              <p className="text-xs text-base-500">{user?.email ?? 'نامشخص'}</p>
            </div>
            <Button variant="ghost" className="text-base-700" onClick={handleLogout}>
              خروج
            </Button>
          </div>
        </div>
      </header>

      <div className="mx-auto grid max-w-7xl grid-cols-1 gap-6 px-6 py-8 lg:grid-cols-[240px_1fr]">
        <aside className="rounded-2xl border border-base-100 bg-white/80 p-4 shadow-card">
          <nav className="flex flex-col gap-2">
            {nav.map((item) => (
              <Link
                key={item.href}
                href={item.href}
                className={cn(
                  'rounded-xl px-4 py-2 text-sm font-medium transition',
                  pathname === item.href
                    ? 'bg-teal-500 text-white'
                    : 'text-base-700 hover:bg-base-50'
                )}
              >
                {item.label}
              </Link>
            ))}
          </nav>
        </aside>
        <main>{children}</main>
      </div>
    </div>
  );
}
