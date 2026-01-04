# SPEC — filament-commerce-experience

## معرفی
- پکیج: haida/filament-commerce-experience
- توضیح: Commerce experience module (reviews/CSAT).
- Service Provider: Haida\FilamentCommerceExperience\FilamentCommerceExperienceServiceProvider
- Filament Plugin: Haida\FilamentCommerceExperience\FilamentCommerceExperiencePlugin (id: filament-commerce-experience)

## دامنه و قابلیت‌ها
- مدل‌ها:
- ExperienceAnswer.php
- ExperienceBuyNowPreference.php
- ExperienceCsatResponse.php
- ExperienceCsatSurvey.php
- ExperienceNpsResponse.php
- ExperienceNpsSurvey.php
- ExperienceQuestion.php
- ExperienceReview.php
- ExperienceReviewVote.php
- منابع Filament:
- src/Filament/Resources/ExperienceBuyNowPreferenceResource.php
- src/Filament/Resources/ExperienceCsatSurveyResource.php
- src/Filament/Resources/ExperienceQuestionResource.php
- src/Filament/Resources/ExperienceReviewResource.php
- کنترلرها/API:
- Api/V1/BuyNowController.php
- Api/V1/CsatController.php
- Api/V1/OpenApiController.php
- Api/V1/QuestionController.php
- Api/V1/ReviewController.php
- Jobs/Queue:
- ندارد
- Policyها:
- ExperienceBuyNowPreferencePolicy.php
- ExperienceCsatSurveyPolicy.php
- ExperienceQuestionPolicy.php
- ExperienceReviewPolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): experience.buy_now.manage, experience.csat.manage, experience.reviews.view

## مدل داده
- Migrations:
- 2026_01_02_000005_create_experience_tables.php
- جدول‌ها:
- ندارد
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-commerce-experience/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-commerce-experience/config/filament-commerce-experience.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
