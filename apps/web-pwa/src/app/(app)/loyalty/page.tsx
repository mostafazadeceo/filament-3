import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

export default function LoyaltyPage() {
  return (
    <div className="space-y-4">
      <Card>
        <CardTitle>باشگاه مشتریان</CardTitle>
        <CardDescription>امتیازدهی، کوپن و دعوت سریع برای مشتریان.</CardDescription>
        <div className="mt-4 flex gap-3">
          <Button>ثبت امتیاز سریع</Button>
          <Button variant="secondary">ری‌دییم کوپن</Button>
        </div>
      </Card>

      <Card>
        <CardTitle>قوانین انقضا</CardTitle>
        <CardDescription>قوانین انقضا و مصرف به‌صورت tenant-based تنظیم می‌شوند.</CardDescription>
      </Card>
    </div>
  );
}
