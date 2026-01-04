# SYSTEM_OVERVIEW

این سند نمای کلی معماری پلتفرم را برای پنل‌ها، ماژول‌ها، APIها و جریان‌های async ارائه می‌دهد.

## اصول کلیدی
- معماری package-first با Filament Plugin برای ثبت منابع/صفحات/ویجت‌ها.
- پنل‌های مجزا برای Admin و Tenant با کنترل دسترسی مبتنی بر IAM.
- APIهای نسخه‌بندی‌شده `/api/v1/*` با scope و rate limit.
- پردازش async برای وبهوک‌ها، سینک‌ها و اعلان‌ها.

## نمای کلان (Mermaid)
```mermaid
flowchart LR
    subgraph Panels
        Admin[Admin Panel] --> AdminPlugins[Filament Plugins]
        Tenant[Tenant Panel] --> TenantPlugins[Filament Plugins]
    end

    subgraph CorePackages[Core Packages]
        IAM[filamat-iam-suite]
        Notify[filament-notify-core + channels]
        Platform[platform-core/site-builder-core]
    end

    subgraph DomainModules[Domain Modules]
        Commerce[commerce-* + filament-commerce-*]
        Workhub[filament-workhub]
        Payments[filament-payments + payments-orchestrator]
        Providers[providers-* + filament-providers-esim-go]
        CMS[content-cms + blog + page-builder]
        Crypto[filament-crypto-*]
        Ops[filament-restaurant-ops + payroll/accounting IR]
    end

    subgraph API
        APIGW[API v1 Routes]
        OpenAPI[Filament API Docs Builder]
    end

    subgraph Async
        Jobs[Jobs/Queues]
        Webhooks[Webhook Ingestion]
        Events[Domain Events]
    end

    AdminPlugins --> CorePackages
    TenantPlugins --> CorePackages
    AdminPlugins --> DomainModules
    TenantPlugins --> DomainModules

    DomainModules --> APIGW
    APIGW --> OpenAPI

    DomainModules --> Jobs
    Webhooks --> Jobs
    Events --> Jobs

    CorePackages --> DB[(Database)]
    DomainModules --> DB
```

## توضیح لایه‌ها
- **Panels**: ثبت پلاگین‌ها و ارائه UI/Resources برای Admin و Tenant.
- **CorePackages**: IAM، Notification، و سرویس‌های پایه.
- **DomainModules**: دامنه‌های اصلی کسب‌وکار (Commerce/Workhub/Payments/Providers/...).
- **API**: لایه ورودی برای موبایل/سرویس‌ها با OpenAPI قابل انتشار.
- **Async**: صف‌ها و پردازش غیرهمزمان برای وبهوک و رویداد.
