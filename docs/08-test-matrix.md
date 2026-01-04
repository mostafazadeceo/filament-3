# Test Matrix (PR-004)

## Feature Gates
- Tenant override allow/deny is prioritized over plan rules.
- Plan feature records enable/disable features per plan.
- Fallback to subscription plan JSON features when plan features table has no record.
- AccessService احترام Feature Gate را حتی با PermissionOverride حفظ می‌کند.

## Automated Coverage
- `Tests\Feature\FeatureGates\FeatureGateServiceTest`
  - `test_tenant_override_has_priority`
  - `test_plan_feature_record_controls_access`
  - `test_fallback_uses_plan_features_json`
  - `test_plan_feature_window_blocks_access`
  - `test_override_outside_window_falls_back_to_plan`
  - `test_limits_are_returned_from_overrides_and_plan`
- `Tests\Feature\FeatureGates\AccessServiceGateTest`
  - `test_feature_gate_blocks_permission_even_with_override`

## Site Builder Core
- `Tests\Feature\SiteBuilderCore\SitePublisherTest`
  - `test_publish_updates_status_and_history`
  - `test_tenant_scope_limits_sites`

## Tenancy Domains
- `Tests\Feature\TenancyDomains\ResolveTenantFromHostTest`
  - `test_resolves_tenant_by_subdomain`
  - `test_resolves_tenant_by_verified_domain`
  - `test_unknown_host_is_blocked`
  - `test_allowed_host_is_accepted`
- `Tests\Feature\TenancyDomains\SiteDomainVerificationTest`
  - `test_request_verification_generates_token`
  - `test_verify_marks_domain_verified`
  - `test_verify_marks_domain_failed`
- `Tests\Feature\TenancyDomains\SiteDomainTlsTest`
  - `test_request_tls_updates_status`

## Theme Engine
- `Tests\Feature\ThemeEngine\ThemeRegistryTest`
  - `test_registry_contains_relograde_theme`

## Page Builder
- `Tests\Feature\PageBuilder\PageBuilderServiceTest`
  - `test_publish_sanitizes_html`
  - `test_publish_requires_sections`

## Content CMS
- `Tests\Feature\ContentCms\CmsPageServiceTest`
  - `test_publish_sanitizes_html`
  - `test_publish_requires_sections`

## Blog
- `Tests\Feature\Blog\BlogPostServiceTest`
  - `test_publish_sanitizes_html`

## Commerce Catalog
- `Tests\Feature\CommerceCatalog\CatalogPricingServiceTest`
  - `test_convert_from_irr_to_usd`
  - `test_convert_from_usd_to_irr`

## Commerce Checkout & Orders
- `Tests\Feature\CommerceCheckout\CheckoutFlowTest`
  - `test_checkout_creates_order_and_wallet_payment` (کاهش موجودی)

## Payments Orchestrator
- `Tests\Feature\PaymentsOrchestrator\WebhookHandlerTest`
  - `test_webhook_is_verified_and_idempotent`
- `Tests\Feature\PaymentsOrchestrator\HmacGatewayAdapterTest`
  - `test_hmac_adapter_creates_intent`

## Providers Core
- `Tests\Feature\ProvidersCore\ProviderActionJobTest`
  - `test_job_updates_log_on_success`
- `Tests\Feature\ProvidersCore\ProviderJobReprocessTest`
  - `test_reprocess_creates_new_log_and_dispatches_job`

## Provider eSIM Go
- `Tests\Feature\Providers\EsimGoCatalogueServiceTest`
  - `test_sync_catalogue_persists_products`
- `Tests\Feature\Providers\EsimGoWebhookControllerTest`
  - `test_webhook_valid_signature_persists_callback`
  - `test_webhook_location_event_is_ignored`

## Observability
- `Tests\Unit\Observability\CorrelationIdFactoryTest`
  - `test_uses_header_value`
  - `test_generates_when_missing`
