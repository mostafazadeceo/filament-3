# DATA_MODEL

## نمای کلی
- تعداد کل جدول‌ها: 321
- تعداد فایل‌های migration/settings: 271

## خلاصه جدول‌ها بر اساس prefix
| Prefix | تعداد جدول | نمونه |
| --- | --- | --- |
| `accounting` | 70 | accounting_ir_account_plans, accounting_ir_account_types, accounting_ir_audit_events |
| `payroll` | 61 | payroll_advances, payroll_ai_logs, payroll_allowance_tables |
| `loyalty` | 29 | loyalty_audit_events, loyalty_badge_awards, loyalty_badges |
| `workhub` | 21 | workhub_ai_field_runs, workhub_ai_summaries, workhub_attachments |
| `restaurant` | 20 | restaurant_goods_receipt_lines, restaurant_goods_receipts, restaurant_inventory_balances |
| `crypto` | 18 | crypto_accounts, crypto_addresses, crypto_ai_reports |
| `petty` | 14 | petty_cash_action_logs, petty_cash_ai_suggestions, petty_cash_audit_events |
| `relograde` | 12 | relograde_accounts, relograde_alerts, relograde_api_logs |
| `iam` | 11 | iam_ai_action_proposals, iam_ai_reports, iam_impersonation_sessions |
| `meeting` | 9 | meeting_action_items, meeting_agenda_items, meeting_ai_runs |
| `fn` | 6 | fn_channel_settings, fn_delivery_logs, fn_notification_rules |
| `ai` | 3 | ai_feedback, ai_policies, ai_requests |
| `api` | 3 | api_docs, api_key_scopes, api_keys |
| `group` | 3 | group_permission, group_role, group_user |
| `permission` | 3 | permission_overrides, permission_snapshots, permission_templates |
| `access` | 2 | access_request_approvals, access_requests |
| `cache` | 2 | cache, cache_locks |
| `currency` | 2 | currency_rate_runs, currency_rates |
| `model` | 2 | model_has_permissions, model_has_roles |
| `wallet` | 2 | wallet_holds, wallet_transactions |
| `webhook` | 2 | webhook_deliveries, webhook_nonces |
| `audit` | 1 | audit_logs |
| `delegated` | 1 | delegated_admin_scopes |
| `failed` | 1 | failed_jobs |
| `groups` | 1 | groups |
| `job` | 1 | job_batches |
| `jobs` | 1 | jobs |
| `meetings` | 1 | meetings |
| `notifications` | 1 | notifications |
| `organizations` | 1 | organizations |
| `otp` | 1 | otp_codes |
| `password` | 1 | password_reset_tokens |
| `permissions` | 1 | permissions |
| `personal` | 1 | personal_access_tokens |
| `role` | 1 | role_has_permissions |
| `roles` | 1 | roles |
| `security` | 1 | security_events |
| `sessions` | 1 | sessions |
| `settings` | 1 | settings |
| `subscription` | 1 | subscription_plans |
| `subscriptions` | 1 | subscriptions |
| `tenant` | 1 | tenant_user |
| `tenants` | 1 | tenants |
| `user` | 1 | user_profiles |
| `users` | 1 | users |
| `wallets` | 1 | wallets |
| `webhooks` | 1 | webhooks |

## جداول کلیدی (نمونه)
- IAM: `tenants`, `roles`, `permissions`, `wallets`, `subscriptions`, `webhooks`, `audit_logs`
- Commerce: `commerce_*`, `catalog_*`, `orders_*`, `checkout_*`
- Workhub: `workhub_*`
- Payments/Crypto: `payment_*`, `crypto_*`
- Providers: `providers_*`, `esim_go_*`

## ایندکس‌ها
- ایندکس‌های هدفمند در migrations برای `tenant_id`, `status`, `updated_at`, و FKها تعریف می‌شوند.
- جزئیات ایندکس‌ها در `docs/_reference/MIGRATIONS/MIGRATION_GUIDE.md` ثبت شده است.

[ASSUMPTION] این خلاصه بر اساس اسکن migrations است؛ برای جزئیات دقیق هر جدول به فایل‌های migration مراجعه شود.
