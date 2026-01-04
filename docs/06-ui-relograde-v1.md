# UI Spec — relograde-v1

## هدف
- ایجاد تم بازاریابی سبک Relograde با تاکید بر آرامش، وضوح و اعتماد.
- سازگار با RTL و تایپوگرافی فارسی.

## توکن ها
- Primary: #E84140
- Background: #F9F9FB
- Dark: #0D0D0D
- Accent: #EC4899
- Accent Secondary: #8B5CF6
- Text: #0F172A
- Muted: #64748B

## ساختار صفحه فرود
- ناوبری: لوگو + لینک ها (برندها/کاربردها/توسعه دهندگان) + ورود + شروع کنید.
- هیرو: تیتر بزرگ، توضیح کوتاه، CTA اصلی، کارت کنسول.
- گرید ویژگی ها: سه کارت پایه.
- کارت گرادیانت: معرفی سناریوها.
- بخش API تاریک با کد نمونه.
- گرید قدرت های اصلی.
- فوتر ساده با کپی رایت.

## اجزای کلیدی
- دکمه اصلی با رنگ Coral و سایه نرم.
- کارت ها با شعاع 18-28px و border ملایم.
- گرادیانت های کنترل شده برای عمق بصری.

## مسیر پیاده سازی
- قالب در `packages/theme-engine/resources/views/themes/relograde-v1/landing.blade.php`.
- CSS در `packages/theme-engine/resources/assets/relograde-v1.css`.
- انتشار دارایی ها با `php artisan vendor:publish --tag=theme-engine-assets`.
