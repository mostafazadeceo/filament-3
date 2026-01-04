# 20260103 — Package-First Architecture

## Context and Problem Statement
پلتفرم شامل دامنه‌های متعدد (Commerce، IAM، Workhub، Payments، Providers و ...) است. توسعه در هسته اپلیکیشن، ریسک coupling و پیچیدگی مدیریت را بالا می‌برد.

## Considered Options
- توسعه مستقیم در اپلیکیشن میزبان (host app)
- بسته‌بندی هر دامنه به‌صورت Laravel package با Filament Plugin
- تفکیک بر اساس سرویس‌های جداگانه (microservices)

## Decision Outcome
معماری package-first انتخاب شد. هر ماژول در `packages/<module>` پیاده‌سازی می‌شود و با Filament Plugin به پنل‌ها متصل است.

## Consequences
- ایزوله‌سازی ماژول‌ها و توسعه مستقل ساده‌تر می‌شود.
- ثبت migrations/config/routes با `laravel-package-tools` استاندارد می‌شود.
- نیاز به هماهنگی در IAM و capability registry برای هر ماژول الزامی است.
