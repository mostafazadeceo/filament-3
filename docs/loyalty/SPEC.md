# Loyalty Club Specification

## Overview
ماژول باشگاه مشتریان یک لایه وفاداری چند-اجاره‌ای برای امتیاز، سطح‌بندی، پاداش‌ها، کوپن، معرفی، کمپین و بازی‌سازی است. منطق اصلی از طریق Rule Engine و Event Ingestion پیاده‌سازی می‌شود و همه عملیات‌ها tenant-scoped و مجوزدار هستند.

## Data Model (table prefix: loyalty_)
- `loyalty_customers`: پروفایل مشتری + ترجیحات بازاریابی و رضایت‌ها.
- `loyalty_tiers`: سطوح وفاداری با آستانه امتیاز/مبلغ.
- `loyalty_customer_tiers`: تاریخچه سطح مشتری.
- `loyalty_points_rules`: قوانین امتیازدهی (fixed/percent, caps, scope).
- `loyalty_events`: ورودی Rule Engine + idempotency.
- `loyalty_wallet_accounts`: مانده‌ها (امتیاز/کش‌بک) + جمع کل‌ها.
- `loyalty_wallet_ledgers`: دفتر کل امتیاز/کش‌بک + ارجاع به رویداد.
- `loyalty_points_buckets`: سبد امتیاز با تاریخ انقضا.
- `loyalty_points_consumptions`: تخصیص مصرف به سبدها.
- `loyalty_rewards`: کاتالوگ پاداش‌ها.
- `loyalty_reward_redemptions`: بازخرید پاداش.
- `loyalty_donation_pledges`: تعهد خیریه برای پاداش‌های donation.
- `loyalty_coupons`, `loyalty_coupon_redemptions`: کوپن/ووچر و سوابق.
- `loyalty_referral_programs`, `loyalty_referrals`: برنامه معرفی و رویدادهای معرفی.
- `loyalty_missions`, `loyalty_mission_progress`, `loyalty_badges`, `loyalty_badge_awards`: بازی‌سازی.
- `loyalty_segments`, `loyalty_customer_segments`: سگمنت‌ها و عضویت.
- `loyalty_campaigns`, `loyalty_campaign_segments`, `loyalty_campaign_variants`, `loyalty_campaign_dispatches`: کمپین‌ها.
- `loyalty_audit_events`: لاگ ممیزی غیرقابل‌ویرایش.
- `loyalty_fraud_signals`: صندوق تخلف و سیگنال‌های ریسک.
- `loyalty_customer_metrics`: شاخص‌های RFM.

## Rule Engine
- ورودی: `LoyaltyEvent` با `type`, `payload`, `idempotency_key`.
- قوانین امتیاز در `loyalty_points_rules` ذخیره می‌شوند و بر اساس event_type و شرایط اعمال می‌شوند.
- safety: caps روزانه/هفتگی، idempotency و allowlist منابع رویداد.
- خروجی: ثبت در `loyalty_wallet_ledgers` و سبدهای امتیاز.

### Event Types
- `purchase_completed`
- `wallet_topup` (اختیاری)
- `referral_completed`
- `birthday`
- `mission_completed`
- `manual_adjustment`
- `refund` / `reversal`

## Wallet + Ledger
- امتیاز همیشه داخلی است (ledger + buckets).
- کش‌بک از طریق Adapter قابل اتصال به Wallet اصلی است.
- تمام تغییرات با تراکنش و lock انجام می‌شود.
- استراتژی انقضا می‌تواند `fixed` یا `inactivity` باشد (در حالت inactivity تاریخ انقضای سبدها با فعالیت تمدید می‌شود).

## Tiers
- تعیین سطح براساس `threshold_points` و `threshold_spend`.
- تاریخچه تغییرات در `loyalty_customer_tiers` ثبت می‌شود.

## Rewards + Redemption
- پاداش‌ها می‌توانند تخفیف، ارسال رایگان، تجربه، کارت هدیه یا خیریه باشند.
- بازخرید باعث ثبت `loyalty_reward_redemptions` و در صورت نیاز صدور کوپن می‌شود.
- انقضا و موجودی کنترل می‌شود.

## Referrals + Anti-Fraud
- کد معرفی با پیشوند قابل تنظیم ایجاد می‌شود.
- ضد تقلب: جلوگیری از self-referral + سقف معرفی در دوره.
- تخلفات به `loyalty_fraud_signals` ارسال می‌شود.

## Segments + Campaigns
- سگمنت‌ها بر اساس قوانین یا RFM ساخته می‌شوند.
- کمپین‌ها با variant و کانال ارسال از notify-core dispatch می‌شوند.
- خروجی API: offers per customer.

## Gamification
- ماموریت‌ها بر اساس criteria رویداد تکمیل و پاداش صادر می‌کنند.
- اعطای badge در `loyalty_badge_awards` ثبت می‌شود.

## Notifications
- ارسال رویدادها از طریق `TriggerDispatcher` و پنل tenant انجام می‌شود.
- اعلان انقضای امتیاز از `loyalty_points_expiring` استفاده می‌کند.

## Integrations / Adapters
- `PurchaseAdapterInterface`: اتصال به سفارش‌ها/صورتحساب‌ها (پیش‌فرض CommerceOrders + fallback).
- `WalletAdapterInterface`: اتصال به کیف پول (پیش‌فرض داخلی، IAM wallet اختیاری).

## Retention
- نگهداری audit/events/fraud بر اساس config.
- عدم ذخیره PII غیرضروری و redaction در لاگ‌ها.
