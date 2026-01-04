# SPEC — filament-petty-cash-ir

## معرفی
- پکیج: haida/filament-petty-cash-ir
- توضیح: Petty cash management for Iranian restaurants with Filament v4.
- Service Provider: Haida\FilamentPettyCashIr\FilamentPettyCashIrServiceProvider
- Filament Plugin: Haida\FilamentPettyCashIr\FilamentPettyCashIrPlugin (id: petty-cash-ir)

## دامنه و قابلیت‌ها
- مدل‌ها:
- PettyCashAiSuggestion.php
- PettyCashAuditEvent.php
- PettyCashCashCount.php
- PettyCashCategory.php
- PettyCashControlException.php
- PettyCashExpense.php
- PettyCashExpenseAttachment.php
- PettyCashFund.php
- PettyCashReconciliation.php
- PettyCashReplenishment.php
- PettyCashSettlement.php
- PettyCashSettlementItem.php
- PettyCashWorkflowRule.php
- منابع Filament:
- src/Filament/Resources/PettyCashCashCountResource.php
- src/Filament/Resources/PettyCashCategoryResource.php
- src/Filament/Resources/PettyCashControlExceptionResource.php
- src/Filament/Resources/PettyCashExpenseResource.php
- src/Filament/Resources/PettyCashFundResource.php
- src/Filament/Resources/PettyCashReconciliationResource.php
- src/Filament/Resources/PettyCashReplenishmentResource.php
- src/Filament/Resources/PettyCashSettlementResource.php
- src/Filament/Resources/PettyCashWorkflowRuleResource.php
- کنترلرها/API:
- Api/V1/AiController.php
- Api/V1/ApiController.php
- Api/V1/CategoryController.php
- Api/V1/ExpenseController.php
- Api/V1/FundController.php
- Api/V1/OpenApiController.php
- Api/V1/ReplenishmentController.php
- Api/V1/SettlementController.php
- Jobs/Queue:
- ندارد
- Policyها:
- PettyCashCashCountPolicy.php
- PettyCashCategoryPolicy.php
- PettyCashControlExceptionPolicy.php
- PettyCashExpensePolicy.php
- PettyCashFundPolicy.php
- PettyCashReconciliationPolicy.php
- PettyCashReplenishmentPolicy.php
- PettyCashSettlementPolicy.php
- PettyCashWorkflowRulePolicy.php

## Tenancy و IAM
- BelongsToTenant در کد: بله
- TenantContext در کد: بله
- IamAuthorization::allows در کد: بله
- Capability Registry: بله
- Scopeها (API): petty_cash.ai.use, petty_cash.ai.view_reports, petty_cash.expense.approve, petty_cash.expense.manage, petty_cash.expense.post, petty_cash.expense.reject, petty_cash.replenishment.approve, petty_cash.replenishment.manage, petty_cash.replenishment.post, petty_cash.replenishment.reject, petty_cash.settlement.approve, petty_cash.settlement.manage, petty_cash.settlement.post

## مدل داده
- Migrations:
- 2026_01_01_000001_create_petty_cash_core_tables.php
- 2026_01_01_000002_create_petty_cash_expense_tables.php
- 2026_01_01_000003_create_petty_cash_settlement_tables.php
- 2026_01_01_000004_create_petty_cash_audit_tables.php
- 2026_01_01_000005_create_petty_cash_action_logs.php
- 2026_01_01_000006_add_reversal_fields_to_petty_cash_tables.php
- 2026_01_01_000007_create_petty_cash_workflow_rules.php
- 2026_01_01_000008_add_workflow_fields_to_petty_cash_tables.php
- 2026_01_01_000009_create_petty_cash_control_tables.php
- 2026_01_01_000010_add_hash_to_petty_cash_expense_attachments.php
- 2026_01_01_000011_create_petty_cash_ai_suggestions.php
- جدول‌ها:
- petty_cash_action_logs
- petty_cash_ai_suggestions
- petty_cash_audit_events
- petty_cash_cash_counts
- petty_cash_categories
- petty_cash_control_exceptions
- petty_cash_expense_attachments
- petty_cash_expenses
- petty_cash_funds
- petty_cash_reconciliations
- petty_cash_replenishments
- petty_cash_settlement_items
- petty_cash_settlements
- petty_cash_workflow_rules
- ایندکس‌ها: دارای ایندکس در مهاجرت‌ها

## API
- مسیر پایه: v1
- OpenAPI: دارای مسیر /openapi
- جزئیات: `docs/filament-petty-cash-ir/API.md`

## تنظیمات
- فایل‌های کانفیگ:
- packages/filament-petty-cash-ir/config/filament-petty-cash-ir.php
- کلیدهای env مرتبط:
- ندارد

## استقرار در پنل‌ها
- Admin Panel: ثبت شده در AdminPanelProvider
- Tenant Panel: ثبت شده در TenantPanelProvider
