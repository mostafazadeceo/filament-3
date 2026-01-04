# Payments Specification

## Scope
Payments package provides payment intents, provider integrations, webhook processing, refunds, and reconciliation. It supports Iranian gateways, international redirect flows, and manual external terminal payments.

## Domain entities
- PaymentIntent
- PaymentAttempt
- PaymentRefund
- PaymentWebhookEvent
- PaymentProviderConnection
- PaymentReconciliation

## Provider interface
- createIntent
- redirectUrl
- verifyCallback
- handleWebhook
- refund
- reconcile

## Providers
- Iranian REST gateway (implemented)
- Iranian SOAP/WSDL (skeleton)
- International redirect/webhook (skeleton)
- Manual/ExternalTerminal (implemented)

## Idempotency
- Payment intents and refunds support idempotency keys.
- Webhook processing is deduplicated by event signature/hash.
