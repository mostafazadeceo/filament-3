<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Filamat\IamSuite\Models\IamAiActionProposal;
use Filamat\IamSuite\Models\IamAiReport;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Services\Automation\IamEventFactory;
use Filamat\IamSuite\Services\Automation\IamEventPublisher;
use Filamat\IamSuite\Services\WebhookService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class N8nCallbackController
{
    public function __construct(
        protected WebhookService $webhookService,
        protected IamEventFactory $eventFactory,
        protected IamEventPublisher $publisher,
    ) {}

    public function __invoke(Request $request): Response
    {
        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            $this->publisher->publish($this->eventFactory->fromAutomationCallbackFailed(null, [
                'reason_code' => 'missing_tenant',
                'correlation_id' => $request->input('correlation_id'),
                'ip' => $request->ip(),
            ]));

            return response(['message' => 'شناسه فضای کاری الزامی است.'], 422);
        }

        $connector = $this->resolveConnector($request, $tenantId);
        if (! $this->isAuthorized($request, $connector)) {
            $this->publisher->publish($this->eventFactory->fromAutomationCallbackFailed($tenantId, [
                'reason_code' => 'unauthorized',
                'correlation_id' => $request->input('correlation_id'),
                'ip' => $request->ip(),
            ]));

            return response(['message' => 'احراز هویت نامعتبر است.'], 401);
        }

        $idempotencyKey = (string) $request->input('idempotency_key');
        if ($idempotencyKey === '') {
            return response(['message' => 'کلید یکتا الزامی است.'], 422);
        }

        $reportPayload = $request->input('report');
        $proposalPayload = $request->input('proposal');

        if (! $reportPayload && ! $proposalPayload) {
            return response(['message' => 'گزارش یا پیشنهاد اقدام الزامی است.'], 422);
        }

        if ($reportPayload) {
            $exists = IamAiReport::query()
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->exists();

            if ($exists) {
                return response(['message' => 'این درخواست قبلاً ثبت شده است.'], 409);
            }

            $report = $this->storeReport($tenantId, $idempotencyKey, $reportPayload, $request);
            $this->publisher->publish($this->eventFactory->fromAutomationReportReceived($tenantId, [
                'id' => $report->getKey(),
                'title' => $report->title,
                'severity' => $report->severity,
                'findings_count' => is_array($report->findings_json) ? count($report->findings_json) : 0,
                'correlation_id' => $report->correlation_id,
            ]));
        }

        if ($proposalPayload) {
            if (! (bool) config('filamat-iam.automation.action_proposals.enabled', true)) {
                return response(['message' => 'پیشنهاد اقدام غیرفعال است.'], 403);
            }

            $exists = IamAiActionProposal::query()
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->exists();

            if ($exists) {
                return response(['message' => 'این درخواست قبلاً ثبت شده است.'], 409);
            }

            $proposal = $this->storeProposal($tenantId, $idempotencyKey, $proposalPayload, $request);
            $this->publisher->publish($this->eventFactory->fromAutomationProposalReceived($tenantId, [
                'id' => $proposal->getKey(),
                'action_type' => $proposal->action_type,
                'status' => $proposal->status,
            ]));
        }

        return response(['status' => 'ok'], 200);
    }

    protected function isAuthorized(Request $request, ?Webhook $connector): bool
    {
        $authMode = (string) config('filamat-iam.automation.inbound.auth_mode', 'header');
        $signature = $this->headerValue($request, $this->headerName('filamat-iam.webhooks.signature_header', 'X-Filamat-Signature'));
        $timestamp = (int) $this->headerValue($request, $this->headerName('filamat-iam.webhooks.timestamp_header', 'X-Filamat-Timestamp'));
        $nonce = $this->headerValue($request, $this->headerName('filamat-iam.webhooks.nonce_header', 'X-Filamat-Nonce'));

        if ($authMode === 'hmac+nonce') {
            if (! $signature || ! $timestamp || ! $nonce || ! $connector) {
                return false;
            }

            return $this->webhookService->verifySignature(
                (string) $connector->secret,
                $request->all(),
                $signature,
                $timestamp,
                $nonce,
                'automation',
                $connector->getKey()
            );
        }

        if ($authMode === 'header') {
            return $this->matchesStaticToken($request);
        }

        if ($authMode === 'none') {
            return true;
        }

        if ($signature && $timestamp && $nonce && $connector) {
            return $this->webhookService->verifySignature(
                (string) $connector->secret,
                $request->all(),
                $signature,
                $timestamp,
                $nonce,
                'automation',
                $connector->getKey()
            );
        }

        return $this->matchesStaticToken($request);
    }

    protected function matchesStaticToken(Request $request): bool
    {
        $headerName = (string) config('filamat-iam.automation.inbound.token_header', 'X-N8N-Token');
        $token = (string) config('filamat-iam.automation.inbound.token', '');
        if ($token === '') {
            return false;
        }

        if ($headerName === '') {
            $headerName = 'X-N8N-Token';
        }

        return hash_equals($token, $this->headerValue($request, $headerName));
    }

    protected function resolveConnector(Request $request, ?int $tenantId): ?Webhook
    {
        $connectorId = $request->input('connector_id')
            ?? $request->input('webhook_id')
            ?? $request->input('context.connector_id');

        $query = Webhook::query()
            ->where('type', (string) config('filamat-iam.automation.webhook_type', 'automation'))
            ->where('enabled', true);

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        if ($connectorId) {
            return $query->whereKey($connectorId)->first();
        }

        return $query->orderByDesc('id')->first();
    }

    protected function storeReport(?int $tenantId, string $idempotencyKey, mixed $payload, Request $request): IamAiReport
    {
        $reportArray = is_array($payload) ? $payload : ['markdown' => (string) $payload];
        $title = (string) ($request->input('title') ?? Arr::get($reportArray, 'title', 'گزارش هوش'));
        $severity = (string) ($request->input('severity') ?? Arr::get($reportArray, 'severity', 'low'));
        $findings = Arr::get($reportArray, 'findings', []);
        $correlationId = $request->input('correlation_id');

        if ($correlationId) {
            $existing = IamAiReport::query()
                ->where('tenant_id', $tenantId)
                ->where('correlation_id', $correlationId)
                ->where('status', 'pending')
                ->first();

            if ($existing) {
                $existing->update([
                    'title' => $title,
                    'body' => Arr::get($reportArray, 'markdown'),
                    'severity' => $severity,
                    'findings_json' => is_array($findings) ? $findings : [$findings],
                    'status' => 'received',
                    'idempotency_key' => $idempotencyKey,
                ]);

                return $existing;
            }
        }

        return IamAiReport::query()->create([
            'tenant_id' => $tenantId,
            'title' => $title,
            'body' => Arr::get($reportArray, 'markdown'),
            'severity' => $severity,
            'findings_json' => is_array($findings) ? $findings : [$findings],
            'status' => 'received',
            'idempotency_key' => $idempotencyKey,
            'correlation_id' => $correlationId,
        ]);
    }

    protected function storeProposal(?int $tenantId, string $idempotencyKey, mixed $payload, Request $request): IamAiActionProposal
    {
        $proposalArray = is_array($payload) ? $payload : [];

        return IamAiActionProposal::query()->create([
            'tenant_id' => $tenantId,
            'report_id' => $request->input('report_id'),
            'action_type' => (string) Arr::get($proposalArray, 'action_type'),
            'target' => Arr::get($proposalArray, 'target'),
            'reason' => Arr::get($proposalArray, 'reason'),
            'requires_approval' => (bool) Arr::get($proposalArray, 'requires_approval', true),
            'status' => 'pending',
            'idempotency_key' => $idempotencyKey,
            'correlation_id' => $request->input('correlation_id'),
        ]);
    }

    protected function headerName(string $configKey, string $default): string
    {
        $value = (string) config($configKey, '');

        return $value !== '' ? $value : $default;
    }

    protected function headerValue(Request $request, string $headerName): string
    {
        if ($headerName === '') {
            return '';
        }

        $value = $request->header($headerName);
        if (is_array($value)) {
            $value = reset($value);
        }

        return is_scalar($value) ? (string) $value : '';
    }
}
