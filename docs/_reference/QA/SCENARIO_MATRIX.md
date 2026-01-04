# SCENARIO_MATRIX

## سناریوهای deep_scenario_runner
- IAM: ساخت tenant، کاربر، نقش، مجوز، و invitation.
- Wallet/Subscription: ساخت wallet، تراکنش، hold/capture، و subscription.
- Workhub: پروژه، workflow، status، work item و اتوماسیون.
- Meetings: ساخت جلسه، attendee، transcript، AI recap.
- Commerce: catalog، cart/checkout، orders، refund.
- Payments: intentها، وبهوک‌ها، و وضعیت پرداخت.
- Crypto: invoice/payout، reconcile و وبهوک‌ها.
- POS: فروش، idempotency و sync.
- Providers eSIM Go: اتصال، کاتالوگ، order و webhook.
- Restaurant Ops: purchase request/order، goods receipt، menu sale.
- Accounting/Payroll IR: جریان‌های پایه مالی و HR.
- CMS/Blog: مدیریت محتوا و انتشار.

## ماتریس tenant
- حداقل 2 tenant با داده‌های مستقل.
- سناریوهای cross-tenant باید بلوکه شوند.

## خطاها و Retry
- ورودی نامعتبر API + بررسی error handling.
- شبیه‌سازی خطا در وبهوک و retry.
- بررسی idempotency در عملیات مالی.

مرجع اجرا: `scripts/deep_scenario_runner.php`
