# یادداشت‌های تحویل نهایی

## مسیرهای اجرا
- QA سریع: `./scripts/qa-sanity.sh`
- Smoke رگرسیون: `./scripts/regression-smoke.sh`
- استیجینگ امن: `./scripts/staging-e2e.sh`

## اسناد کلیدی
- معماری: `docs/03-architecture.md`
- ERD: `docs/04-erd.mmd`
- جریان‌ها: `docs/05-workflows.mmd`
- برنامه انتشار: `docs/09-release-plan.md`
- چک‌لیست انتشار: `docs/99-release-checklist.md`

## نکات عملیاتی
- اجرای اسکریپت‌ها روی production ممنوع است.
- در صورت استفاده از دیتابیس استیجینگ واقعی، متغیر `STAGING_ALLOW_MIGRATE=1` لازم است.
