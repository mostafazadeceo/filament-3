<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Automation;

use Filamat\IamSuite\Models\AccessRequest;
use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\AuditLog;
use Filamat\IamSuite\Models\IamAiReport;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\SecurityEvent;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Services\WebhookService;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class IamAiAuditRunner
{
    public function __construct(
        protected WebhookService $webhookService,
        protected IamEventFactory $eventFactory,
        protected IamEventEnvelopeFactory $envelopeFactory,
    ) {}

    /**
     * @return array<int, array<string, mixed>>
     */
    public function run(): array
    {
        $windowDays = (int) config('filamat-iam.automation.audit.window_days', 7);
        $windowStart = now()->subDays($windowDays)->startOfDay();
        $windowEnd = now();
        $type = (string) config('filamat-iam.automation.webhook_type', 'automation');

        $webhooks = Webhook::query()
            ->where('type', $type)
            ->where('enabled', true)
            ->where('is_ai_auditor', true)
            ->get();

        $results = [];

        foreach ($webhooks as $webhook) {
            $tenantId = $webhook->tenant_id;
            if (! $tenantId) {
                continue;
            }

            $tenant = Tenant::query()->find($tenantId);
            if (! $tenant) {
                continue;
            }

            TenantContext::setTenant($tenant);

            $summary = $this->buildSummary($tenantId, $windowStart, $windowEnd);
            $runId = (string) Str::uuid();

            $summary = array_merge($summary, [
                'run_id' => $runId,
                'window_start' => $windowStart->toIso8601String(),
                'window_end' => $windowEnd->toIso8601String(),
            ]);

            $event = $this->eventFactory->fromAutomationAuditStarted($tenantId, $summary);
            $payload = $this->envelopeFactory->build($event, $webhook);
            $payload['context'] = array_merge((array) ($payload['context'] ?? []), [
                'connector_id' => $webhook->getKey(),
            ]);

            $report = IamAiReport::query()->create([
                'tenant_id' => $tenantId,
                'title' => 'گزارش در انتظار',
                'body' => null,
                'severity' => 'low',
                'findings_json' => [],
                'status' => 'pending',
                'idempotency_key' => $payload['idempotency_key'] ?? null,
                'correlation_id' => $runId,
            ]);

            $delivery = $this->webhookService->deliverNow($webhook, $payload);
            $responseBody = $delivery->response['body'] ?? null;

            if (is_array($responseBody)) {
                $reportData = $responseBody['report'] ?? $responseBody;
                $this->applyReportResponse($report, $reportData);

                $completed = $this->eventFactory->fromAutomationAuditCompleted($tenantId, [
                    'run_id' => $runId,
                    'status' => 'succeeded',
                    'findings_count' => is_array($report->findings_json) ? count($report->findings_json) : 0,
                    'severity_max' => $report->severity,
                ]);
                $completedPayload = $this->envelopeFactory->build($completed, $webhook);
                $completedPayload['context'] = array_merge((array) ($completedPayload['context'] ?? []), [
                    'connector_id' => $webhook->getKey(),
                ]);

                $this->webhookService->queue($webhook, $completedPayload);
            }

            $results[] = [
                'tenant_id' => $tenantId,
                'run_id' => $runId,
                'delivery_id' => $delivery->getKey(),
                'status' => $delivery->status,
            ];
        }

        TenantContext::setTenant(null);

        return $results;
    }

    /**
     * @return array<string, mixed>
     */
    protected function buildSummary(int $tenantId, \Illuminate\Support\Carbon $start, \Illuminate\Support\Carbon $end): array
    {
        $securityTotal = SecurityEvent::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('occurred_at', [$start, $end])
            ->count();

        $failedLogins = SecurityEvent::query()
            ->where('tenant_id', $tenantId)
            ->where('type', 'auth.failed')
            ->whereBetween('occurred_at', [$start, $end])
            ->count();

        $impersonations = SecurityEvent::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('type', ['impersonation.start', 'impersonation.stop'])
            ->whereBetween('occurred_at', [$start, $end])
            ->count();

        $permissionOverrides = AuditLog::query()
            ->where('tenant_id', $tenantId)
            ->where('subject_type', PermissionOverride::class)
            ->whereIn('action', ['created', 'updated', 'deleted'])
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $apiKeyRotations = AuditLog::query()
            ->where('tenant_id', $tenantId)
            ->where('subject_type', ApiKey::class)
            ->where('action', 'updated')
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $pendingAccessRequests = AccessRequest::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->count();

        return [
            'security_events_total' => $securityTotal,
            'failed_logins' => $failedLogins,
            'impersonation_events' => $impersonations,
            'permission_override_changes' => $permissionOverrides,
            'api_key_rotations' => $apiKeyRotations,
            'pending_access_requests' => $pendingAccessRequests,
        ];
    }

    protected function applyReportResponse(IamAiReport $report, array $response): void
    {
        $title = (string) Arr::get($response, 'title', $report->title);
        $severity = (string) Arr::get($response, 'severity', $report->severity);
        $findings = Arr::get($response, 'findings', []);

        $report->update([
            'title' => $title,
            'body' => Arr::get($response, 'markdown') ?? Arr::get($response, 'body'),
            'severity' => $severity,
            'findings_json' => is_array($findings) ? $findings : [$findings],
            'status' => 'received',
        ]);
    }
}
