# 20260103 — API Rate Limit & Queue Strategy

## Context and Problem Statement
APIها باید از سوءاستفاده و بار ناگهانی محافظت شوند. پردازش‌های سنگین (وبهوک، سینک، اعلان) باید غیرهمزمان باشند.

## Considered Options
- عدم محدودیت نرخ و پردازش همزمان
- Rate limit سراسری با امکان override در config
- صف‌های جدا برای وبهوک/اعلان/پرداخت با سیاست retry

## Decision Outcome
الگوی نرخ‌محدودیت پیش‌فرض `60,1` در route middleware و امکان override در config ماژول‌ها پذیرفته شد. پردازش‌های سنگین از طریق Jobs انجام می‌شوند و سیاست retry در سطح Queue مدیریت می‌شود.

## Consequences
- هر ماژول باید rate limit خود را شفاف در config مستند کند.
- Runbook باید سناریوهای queue stuck و backlog را پوشش دهد.
