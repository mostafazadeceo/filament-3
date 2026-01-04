# API — 3CX CRM Connector

## احراز هویت
### حالت Connector Key (پیش‌فرض)
- هدر: `X-ThreeCX-Connector-Key: <key>`
- کلید در اتصال 3CX ذخیره می‌شود و به صورت hash اعتبارسنجی می‌گردد.

### حالت API Key
- هدر: `X-Api-Key: <tenant_api_key>`
- در صورت نیاز، `instance_id` را به صورت query ارسال کنید.

## مسیر پایه
`/api/v1/threecx/crm`

## نرخ محدودسازی
پیش‌فرض: `30,1` (قابل تغییر در config).

## Lookup
`GET /lookup`

پارامترها:
- `phone` (اختیاری)
- `email` (اختیاری)

نمونه پاسخ:
```json
{
  "data": [
    {
      "id": "123",
      "name": "مهدی رضایی",
      "phones": ["09120000000"],
      "emails": ["mehdi@example.com"],
      "crm_url": "https://crm.example.com/contacts/123"
    }
  ]
}
```

## Search
`GET /search`

پارامترها:
- `query` یا `q`

## Create Contact
`POST /contacts`

نمونه payload:
```json
{
  "name": "مهدی رضایی",
  "phones": ["09120000000"],
  "emails": ["mehdi@example.com"],
  "external_id": "crm-123",
  "crm_url": "https://crm.example.com/contacts/123"
}
```

## Journal Call
`POST /journal/call`

فیلدهای متداول:
- `direction` (inbound/outbound/missed)
- `from` یا `from_number`
- `to` یا `to_number`
- `started_at`, `ended_at`
- `duration`
- `status`
- `external_id`

## Journal Chat
`POST /journal/chat`

فیلدهای متداول مشابه Journal Call هستند و به عنوان لاگ گفتگو ذخیره می‌شوند.

## نکات نگاشت برای 3CX Wizard
- شناسه مخاطب را روی `id` یا `external_id` نگاشت کنید.
- نام مخاطب: `name`
- شماره‌ها: `phones`
- ایمیل‌ها: `emails`
- لینک CRM: `crm_url`

## OpenAPI ماژول
برای دریافت سند OpenAPI ماژول:
`GET /api/v1/threecx/openapi`

نکته: این مسیر از احراز هویت استاندارد API استفاده می‌کند (ApiKeyAuth/ApiAuth) و به CRM Connector تعلق ندارد.
