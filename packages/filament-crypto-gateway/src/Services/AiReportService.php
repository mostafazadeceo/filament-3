<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Services;

use Haida\FilamentCryptoGateway\Contracts\AiInsightProvider;
use Haida\FilamentCryptoGateway\Models\CryptoAiReport;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Models\CryptoInvoicePayment;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;

class AiReportService
{
    public function __construct(
        protected PlanService $planService,
        protected AiInsightProvider $aiProvider,
    ) {}

    public function generate(int $tenantId, ?string $provider = null, ?string $periodStart = null, ?string $periodEnd = null, string $period = 'weekly'): CryptoAiReport
    {
        $start = $periodStart ? now()->parse($periodStart) : now()->subWeek();
        $end = $periodEnd ? now()->parse($periodEnd) : now();

        $metrics = $this->collectMetrics($tenantId, $provider, $start, $end);
        $summary = $this->generateSummary($metrics);

        if (config('filament-crypto-gateway.ai.enabled', false)
            && $this->planService->allowsFeature($tenantId, 'crypto.ai_auditor')) {
            $aiPayload = $this->aiProvider->generateInsights([
                'tenant_id' => $tenantId,
                'metrics' => $metrics,
            ]);

            if (is_array($aiPayload) && isset($aiPayload['summary'])) {
                $summary = (string) $aiPayload['summary'];
            } elseif (is_array($aiPayload) && isset($aiPayload['summary_md'])) {
                $summary = (string) $aiPayload['summary_md'];
            }

            if (is_array($aiPayload) && isset($aiPayload['anomalies']) && is_array($aiPayload['anomalies'])) {
                $metrics['anomalies'] = array_merge($metrics['anomalies'], $aiPayload['anomalies']);
            }
        }

        $report = CryptoAiReport::query()->create([
            'tenant_id' => $tenantId,
            'period' => $period,
            'report_at' => $end,
            'summary_md' => $summary,
            'payload_json' => $metrics,
            'anomalies_json' => $metrics['anomalies'],
            'status' => 'ready',
            'meta' => [
                'provider' => $provider,
                'period_start' => $start->toIso8601String(),
                'period_end' => $end->toIso8601String(),
            ],
        ]);

        if (class_exists(TriggerDispatcher::class)) {
            $panelId = (string) config('filament-crypto-gateway.notifications.panel', 'tenant');
            $event = (string) config('filament-crypto-gateway.notifications.audit_event', 'crypto.audit.report');
            if ($panelId === '' || $event === '') {
                return $report;
            }
            try {
                app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $report, $event);
            } catch (\Throwable) {
                // Keep reporting resilient.
            }
        }

        return $report;
    }

    /**
     * @return array<string, mixed>
     */
    protected function collectMetrics(int $tenantId, ?string $provider, $start, $end): array
    {
        $invoices = CryptoInvoice::query()
            ->where('tenant_id', $tenantId)
            ->when($provider, fn ($query) => $query->where('provider', $provider))
            ->whereBetween('created_at', [$start, $end]);

        $payouts = CryptoPayout::query()
            ->where('tenant_id', $tenantId)
            ->when($provider, fn ($query) => $query->where('provider', $provider))
            ->whereBetween('created_at', [$start, $end]);

        $webhooks = CryptoWebhookCall::query()
            ->where('tenant_id', $tenantId)
            ->whereBetween('created_at', [$start, $end]);

        $statusCounts = (clone $invoices)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();
        $paidCount = (int) ($statusCounts['paid'] ?? 0);
        $paidOverCount = (int) ($statusCounts['paid_over'] ?? 0);
        $wrongAmountCount = (int) ($statusCounts['wrong_amount'] ?? 0);
        $failedCount = (int) ($statusCounts['failed'] ?? 0);
        $expiredPaidLate = (clone $invoices)
            ->whereIn('status', ['paid', 'paid_over', 'completed'])
            ->whereNotNull('expires_at')
            ->whereColumn('updated_at', '>', 'expires_at')
            ->count();

        $anomalies = [];
        if ($wrongAmountCount > 0) {
            $anomalies[] = ['key' => 'wrong_amount', 'count' => $wrongAmountCount, 'message' => 'پرداخت با مبلغ نادرست ثبت شده است.'];
        }
        if ($paidOverCount > 0) {
            $anomalies[] = ['key' => 'paid_over', 'count' => $paidOverCount, 'message' => 'پرداخت بیش از مبلغ فاکتور مشاهده شد.'];
        }
        if ($failedCount > 0) {
            $anomalies[] = ['key' => 'failed', 'count' => $failedCount, 'message' => 'تعداد قابل توجهی فاکتور ناموفق است.'];
        }
        if ($expiredPaidLate > 0) {
            $anomalies[] = ['key' => 'paid_late', 'count' => $expiredPaidLate, 'message' => 'فاکتورهای منقضی شده که دیر پرداخت شده‌اند.'];
        }

        $webhookFailed = (clone $webhooks)->where('status', 'failed')->count();
        if ($webhookFailed > 0) {
            $anomalies[] = ['key' => 'webhook_failed', 'count' => $webhookFailed, 'message' => 'وبهوک‌های ناموفق نیاز به بازپردازش دارند.'];
        }

        $webhookBacklog = (clone $webhooks)->whereIn('status', ['received', 'processing'])->count();
        if ($webhookBacklog > 0) {
            $anomalies[] = ['key' => 'webhook_backlog', 'count' => $webhookBacklog, 'message' => 'صف پردازش وبهوک‌ها نیاز به بررسی دارد.'];
        }

        $duplicateTx = CryptoInvoicePayment::query()
            ->where('tenant_id', $tenantId)
            ->whereNotNull('txid')
            ->select('txid')
            ->groupBy('txid')
            ->havingRaw('count(*) > 1')
            ->count();

        if ($duplicateTx > 0) {
            $anomalies[] = ['key' => 'duplicate_txid', 'count' => $duplicateTx, 'message' => 'TXID تکراری برای پرداخت‌ها ثبت شده است.'];
        }

        return [
            'period' => [
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
            ],
            'providers' => $provider,
            'invoice_counts' => $statusCounts,
            'paid_count' => $paidCount,
            'paid_over_count' => $paidOverCount,
            'wrong_amount_count' => $wrongAmountCount,
            'paid_late_count' => $expiredPaidLate,
            'failed_count' => $failedCount,
            'payouts_count' => (clone $payouts)->count(),
            'webhooks_failed' => $webhookFailed,
            'webhooks_backlog' => $webhookBacklog,
            'duplicate_txid' => $duplicateTx,
            'anomalies' => $anomalies,
        ];
    }

    protected function generateSummary(array $metrics): string
    {
        $paid = $metrics['paid_count'] ?? 0;
        $failed = $metrics['failed_count'] ?? 0;
        $payouts = $metrics['payouts_count'] ?? 0;
        $wrong = $metrics['wrong_amount_count'] ?? 0;
        $late = $metrics['paid_late_count'] ?? 0;

        return 'خلاصه دوره: '
            .'پرداخت موفق '.$paid.' مورد، '
            .'پرداخت ناموفق '.$failed.' مورد، '
            .'برداشت '.$payouts.' مورد، '
            .'پرداخت نامنطبق '.$wrong.' مورد، '
            .'پرداخت دیرهنگام '.$late.' مورد.';
    }
}
