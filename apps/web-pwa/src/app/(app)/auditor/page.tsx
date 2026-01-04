import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';

const ALERTS = [
  { title: 'افزایش غیرعادی ریفاند', level: 'amber', detail: 'سه مورد طی ۳۰ دقیقه اخیر' },
  { title: 'تاخیر در همگام‌سازی POS', level: 'teal', detail: 'میانگین ۴ دقیقه' }
] as const;

export default function AuditorPage() {
  return (
    <div className="space-y-4">
      <Card>
        <CardTitle>گزارش ممیزی هوشمند</CardTitle>
        <CardDescription>شاخص‌های کلیدی و هشدارهای ریسک اینجا نمایش داده می‌شوند.</CardDescription>
      </Card>

      <div className="grid gap-4 md:grid-cols-2">
        {ALERTS.map((alert) => (
          <Card key={alert.title}>
            <div className="flex items-center justify-between">
              <CardTitle>{alert.title}</CardTitle>
              <Badge tone={alert.level === 'amber' ? 'amber' : 'teal'}>
                {alert.level === 'amber' ? 'نیاز به بررسی' : 'کنترل'}
              </Badge>
            </div>
            <CardDescription className="mt-2">{alert.detail}</CardDescription>
          </Card>
        ))}
      </div>
    </div>
  );
}
