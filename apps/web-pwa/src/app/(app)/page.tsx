import Link from 'next/link';
import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const MODULES = [
  {
    href: '/pos',
    title: 'POS و عملیات فروش',
    description: 'ثبت سفارش آفلاین، شیفت صندوق و همگام‌سازی سریع.',
    badge: 'آفلاین'
  },
  {
    href: '/support',
    title: 'پشتیبانی چندکاناله',
    description: 'تیکت، چت زنده، پیوست‌ها و SLA.',
    badge: 'Realtime'
  },
  {
    href: '/hr',
    title: 'حضور و غیاب هوشمند',
    description: 'چک‌این/آوت آفلاین، تأیید اصلاحات و رضایت‌ها.',
    badge: 'HR'
  },
  {
    href: '/tasks',
    title: 'تسک‌ها و عملیات تیمی',
    description: 'پیگیری کارها، برچسب‌ها و مهلت‌ها.',
    badge: 'Workhub'
  },
  {
    href: '/meetings',
    title: 'جلسات + خروجی اقدام',
    description: 'دستورجلسه، صورت‌جلسه و استخراج تسک.',
    badge: 'AI'
  },
  {
    href: '/crypto',
    title: 'رمزارز و تسویه',
    description: 'وضعیت فاکتور، پرداخت و درخواست تسویه.',
    badge: 'Crypto'
  },
  {
    href: '/loyalty',
    title: 'باشگاه مشتریان',
    description: 'امتیازدهی، کوپن‌ها و ری‌دییم سریع.',
    badge: 'Club'
  },
  {
    href: '/auditor',
    title: 'ممیزی هوشمند',
    description: 'ریسک‌ها، مغایرت‌ها و هشدارهای مدیریتی.',
    badge: 'Audit'
  }
];

export default function AppHome() {
  return (
    <div className="space-y-6">
      <div className="rounded-3xl border border-base-100 bg-white/80 p-6 shadow-card">
        <h2 className="text-2xl font-semibold text-base-900">کابین عملیات هاب</h2>
        <p className="mt-2 text-sm text-base-600">
          مسیرهای سریع برای تیم‌های فروش، پشتیبانی، منابع انسانی و عملیات روزمره.
        </p>
      </div>

      <div className="grid gap-4 md:grid-cols-2">
        {MODULES.map((module) => (
          <Link key={module.href} href={module.href}>
            <Card className="transition hover:-translate-y-1 hover:border-teal-200">
              <div className="flex items-center justify-between">
                <CardTitle>{module.title}</CardTitle>
                <Badge tone="teal">{module.badge}</Badge>
              </div>
              <CardDescription className="mt-2">{module.description}</CardDescription>
            </Card>
          </Link>
        ))}
      </div>
    </div>
  );
}
