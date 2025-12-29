# SPEC — Workhub (Issue & Project Tracking)

## اهداف محصول (Goals)
- تبدیل پروژه و کارهای اجرایی به یک «مرکز واحد کار» با حداقل پراکندگی ابزار.
- کاهش هزینه‌ی توجه (attention cost) و رفت‌وآمد ذهنی با جریان‌های ساده، قابل فهم و شفاف.
- ایجاد همکاری امن و بدون سرزنش: ثبت تصمیم‌ها، زمینه‌ها و تغییرات بدون ابزارهای نظارتی مزاحم.
- تمرکز بر خروجی (Outcome) و نتیجه، نه فعالیت ظاهری.
- اکوسیستم‌پذیر و قابل توسعه برای ماژول‌های فعلی و آینده.

## عدم‌اهداف (Non-goals)
- ابزار مانیتورینگ/ردیابی رفتاری (کیبورد، اسکرین‌شات، ردیابی زمان پنهان).
- جایگزینی کامل ابزارهای مدیریت دانش (صرفا لینک‌دهی و ارجاع).
- اجرای کامل و پیچیده‌ی PMO در MVP.

## پرسونای اصلی
- مدیران ارشد/PMO/Compliance: شفافیت، گزارش‌پذیری، ممیزی.
- مدیران تیم: وضعیت واقعی، ظرفیت، ریسک، برنامه‌ریزی قابل اتکا.
- تیم‌ها: تمرکز، گردش‌کار منعطف، کاهش مزاحمت.
- افراد: مسیر واضح «اکنون/بعد/بعدتر»، کاهش وقفه‌ها و کارهای بی‌اثر.

## مدل دامنه (Domain Model)
- Workspace/Tenant: فضای کاری از IAM Suite.
- Project: پروژه‌های کاری.
- WorkType: نوع کار (با آیکن/رنگ).
- WorkItem: آیتم کاری (issue/task).
- Workflow: گردش‌کار پروژه.
- Status: وضعیت‌ها با دسته‌بندی TODO/IN_PROGRESS/DONE.
- Transition: قوانین جابجایی وضعیت.
- Comment: گفتگوهای مرتبط با WorkItem.
- Attachment: پیوست‌ها.
- Watcher: دنبال‌کننده‌ها.
- Label/Tag: برچسب‌ها.
- TimeEntry: ثبت زمان (غیرنظارتی).
- DecisionLog: ثبت تصمیم.
- AuditEvent: ممیزی تغییرات کلیدی.
- EntityReference: لینک‌دهی به هر مدل Eloquent.
- AutomationRule: Rule (Trigger + Condition + Action).
- CustomField / CustomFieldValue: فیلدهای سفارشی با حاکمیت.

## معماری داده و مهاجرت‌ها
- تمام جدول‌ها با پیشوند `workhub_`.
- `tenant_id` در همه‌ی جداول دامنه (FK + index).
- Soft delete برای Project/WorkItem/Comment/Workflow (مطابق نیاز).
- ایندکس‌های کلیدی: tenant_id, project_id, status_id, assignee_id, due_date, updated_at.
- محدودیت‌ها: یکتایی کلید پروژه در هر تننت، یکتایی شماره آیتم در هر پروژه.
- حاکمیت لینک‌ها در سطح پروژه با `allowed_link_types`.

## مدل امنیتی و دسترسی
- نقش‌ها و مجوزهای ریزدانه با `spatie/laravel-permission` و IAM Suite.
- قوانین در UI و API یکسان و مبتنی بر `IamAuthorization`.
- اسکوپ تننت با `TenantContext` و Global Scope.
- مجوزهای پایه:
  - workhub.project.view/manage
  - workhub.work_item.view/manage
  - workhub.workflow.manage
  - workhub.status.manage
  - workhub.transition.manage
  - workhub.comment.manage
  - workhub.attachment.manage
  - workhub.watcher.manage
  - workhub.label.manage
  - workhub.time_entry.manage
  - workhub.decision.manage
  - workhub.audit.view
  - workhub.automation.manage
  - workhub.link.manage

## نقشه UI (Filament v4)
- Resources:
  - ProjectResource
  - WorkItemResource
  - WorkflowResource
  - StatusResource
  - TransitionResource
  - WorkTypeResource
  - LabelResource
  - CustomFieldResource
  - AutomationRuleResource
- Pages:
  - KanbanBoard (درگ‌ودراپ)
  - WorkItemDetail (تب‌ها: overview, comments, attachments, history)
- Widgets:
  - MyWork (optional)

## API v1 (REST)
- مسیر پایه: `/api/v1/workhub`
- Projects: index/show/store/update/delete
- WorkItems: index/show/store/update/delete
- Transitions: POST /work-items/{id}/transition
- Comments: index/store/delete
- Attachments: index/store/delete
- Watchers: index/store/delete
- Labels: index/store/delete
- TimeEntries: index/store/delete
- Decisions: index/store/delete
- Links: index/store/delete
- Workflows/Statuses/Transitions: index/store/update
- WorkTypes: index/show/store/update/delete
- CustomFields: index/show/store/update/delete
- AutomationRules: index/show/store/update/delete

## وبهوک‌ها و لاگ تحویل
- استفاده از سیستم وبهوک IAM Suite (type = workhub).
- رویدادها: project.created/updated, work_item.created/updated/transitioned, comment.created, attachment.created.
- HMAC signature و ریتری از طریق WebhookService.

## اکستنسیبیلیتی و Work Graph
- EntityReferenceRegistry برای ثبت انواع لینک‌شدنی توسط سایر ماژول‌ها.
- API و UI برای مدیریت لینک‌ها.
- سیاست‌های مجازسازی لینک‌ها به‌صورت per-project (allowed_link_types).

## MVP در برابر فازها
- MVP:
  - پروژه + آیتم کاری + گردش‌کار پایه
  - کانبان + انتقال وضعیت
  - کامنت/پیوست/واچر
  - API v1 + وبهوک‌ها
  - فیلدهای سفارشی + اتوماسیون پایه
- فاز 2:
  - Portfolio/OKR linkage
  - گزارش‌های پیشرفته و ریسک
  - اتوماسیون غنی‌تر
- فاز 3:
  - Marketplace افزونه‌ها
  - سیستم extension برای types/actions/triggers

## فرضیات
- IAM Suite برای مدیریت تننت و مجوزها مبناست.
- API Docs Builder برای مستندسازی OpenAPI استفاده می‌شود.
