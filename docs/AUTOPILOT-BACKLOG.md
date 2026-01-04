# AUTOPILOT Backlog (مرتب‌سازی بر اساس اولویت)

## امنیت/تننت/درگاه (اولویت بالا)
- [x] سخت‌سازی Host: TrustedHosts/ForwardedHost + تست‌های poisoning
- [x] تکمیل DNS Verification + rate limit برای دامنه سفارشی
- [x] افزودن TLS automation hooks + وضعیت صدور/تمدید + runbook
- [x] ممیزی enforcement Feature Gates در UI/Policy/API/Jobs
- [x] رزرو/کاهش موجودی همزمان در checkout (race-safe)
- [x] وبهوک درگاه tenant واقعی (فراتر از Dummy) + idempotency کامل

## هسته پلتفرم
- [x] تکمیل docs چرخه لایف‌سایکل با UI و rollback عملیاتی

## کامرس/Provider
- [x] اتصال کامل سفارش‌ها به Providerها (fulfillment + delivery record)
- [x] dead-letter + UI بازپردازش برای Provider jobs
- [ ] تست‌های همزمانی (parallel checkout) برای رزرو موجودی

## مستندات و کیفیت
- [x] تکمیل «مستندات به‌روز هستند» در /docs/99-release-checklist.md
- [x] تکمیل «rollback plan آماده است» با runbook عملیاتی
- [x] تکمیل/بازبینی test matrix برای سناریوهای جدید

## وابستگی‌های خارجی (PENDING EXTERNAL)
- [ ] دسترسی DNS برای رکوردهای TXT/CNAME و دامنه‌های واقعی
- [ ] اطلاعات ACME/TLS (staging/production)
- [ ] Secrets در CI برای درگاه‌های واقعی و Providerها (در صورت نیاز)
- [ ] کلید API و هدر امضای وبهوک eSIM Go در محیط واقعی
