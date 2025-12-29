# API — Workhub (v1)

## احراز هویت
- هدرها:
  - `Authorization: Bearer <token>`
  - `X-Tenant-ID: <tenant_id>`
- مسیر پایه: `/api/v1/workhub`

## پروژه‌ها
- `GET /projects`
- `POST /projects`
- `GET /projects/{project}`
- `PUT/PATCH /projects/{project}`
- `DELETE /projects/{project}`

## آیتم‌های کاری
- `GET /work-items`
- `POST /work-items`
- `GET /work-items/{workItem}`
- `PUT/PATCH /work-items/{workItem}`
- `DELETE /work-items/{workItem}`

### فیلدهای سفارشی آیتم
- `custom_fields` در بدنه‌ی ایجاد/ویرایش آیتم قابل ارسال است.

## انتقال وضعیت
- `POST /work-items/{workItem}/transition`

## گردش‌کار
- `GET /workflows`
- `POST /workflows`
- `GET /workflows/{workflow}`
- `PUT/PATCH /workflows/{workflow}`
- `DELETE /workflows/{workflow}`

## وضعیت‌ها
- `GET /statuses`
- `POST /statuses`
- `GET /statuses/{status}`
- `PUT/PATCH /statuses/{status}`
- `DELETE /statuses/{status}`

## انتقال‌ها
- `GET /transitions`
- `POST /transitions`
- `GET /transitions/{transition}`
- `PUT/PATCH /transitions/{transition}`
- `DELETE /transitions/{transition}`

## نوع‌های کار
- `GET /work-types`
- `POST /work-types`
- `GET /work-types/{workType}`
- `PUT/PATCH /work-types/{workType}`
- `DELETE /work-types/{workType}`

## همکاری
- `GET /work-items/{workItem}/comments`
- `POST /work-items/{workItem}/comments`
- `DELETE /comments/{comment}`

- `GET /work-items/{workItem}/attachments`
- `POST /work-items/{workItem}/attachments`
- `DELETE /attachments/{attachment}`

- `GET /work-items/{workItem}/watchers`
- `POST /work-items/{workItem}/watchers`
- `DELETE /watchers/{watcher}`

## برچسب‌ها
- `GET /labels`
- `POST /labels`
- `PUT/PATCH /labels/{label}`
- `DELETE /labels/{label}`

## زمان و تصمیم‌ها
- `GET /work-items/{workItem}/time-entries`
- `POST /work-items/{workItem}/time-entries`
- `DELETE /time-entries/{timeEntry}`

- `GET /work-items/{workItem}/decisions`
- `POST /work-items/{workItem}/decisions`
- `DELETE /decisions/{decision}`

## لینک‌ها
- `GET /work-items/{workItem}/links`
- `POST /work-items/{workItem}/links`
- `DELETE /links/{link}`

## فیلدهای سفارشی
- `GET /custom-fields`
- `POST /custom-fields`
- `GET /custom-fields/{customField}`
- `PUT/PATCH /custom-fields/{customField}`
- `DELETE /custom-fields/{customField}`

## اتوماسیون
- `GET /automation-rules`
- `POST /automation-rules`
- `GET /automation-rules/{automationRule}`
- `PUT/PATCH /automation-rules/{automationRule}`
- `DELETE /automation-rules/{automationRule}`

## OpenAPI
- خروجی OpenAPI در مسیر `GET /api/v1/workhub/openapi` ارائه می‌شود.
