import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

export default function CryptoPage() {
  return (
    <div className="space-y-4">
      <Card>
        <CardTitle>فاکتورهای رمزارزی</CardTitle>
        <CardDescription>وضعیت فاکتورها به‌صورت realtime و با fallback polling به‌روزرسانی می‌شود.</CardDescription>
        <div className="mt-4">
          <Button>درخواست تسویه</Button>
        </div>
      </Card>

      <Card>
        <CardTitle>حریم خصوصی پرداخت</CardTitle>
        <CardDescription>TXID و آدرس‌ها با ماسک قابل تنظیم نمایش داده می‌شوند.</CardDescription>
      </Card>
    </div>
  );
}
