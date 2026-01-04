# 20260103 — Webhook Idempotency & State Machines

## Context and Problem Statement
ماژول‌هایی مثل Payments, Crypto, Relograde و IAM از وبهوک‌های بیرونی استفاده می‌کنند. بدون idempotency و state tracking احتمال دوبار پردازش، مغایرت مالی و incident افزایش می‌یابد.

## Considered Options
- پردازش مستقیم وبهوک بدون ذخیره‌سازی رویداد
- ذخیره رویداد + پردازش asynchronous با کنترل تکرار
- صف‌های جداگانه و DLQ سفارشی برای هر ماژول

## Decision Outcome
ثبت رویداد وبهوک و پردازش idempotent با کلید یکتا و صف (Job) انتخاب شد. نمونه‌ها: `WebhookService` در IAM Suite و `WebhookHandler` در Payments/Orchestrator. State machine‌ها (مثلاً وضعیت‌های invoice/payout) به‌عنوان منبع حقیقت پردازش استفاده می‌شوند.

## Consequences
- نیاز به ثبت idempotency key و بررسی replay در سرویس‌ها الزامی است.
- سیستم صف باید پایدار باشد و retryها کنترل‌شده انجام شود.
- مانیتورینگ webhook backlog و DLQ لازم است.
