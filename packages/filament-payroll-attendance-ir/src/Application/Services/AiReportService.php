<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Filamat\IamSuite\Support\IamAuthorization;
use Vendor\FilamentPayrollAttendanceIr\Infrastructure\Ai\AiProviderInterface;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAiLog;

class AiReportService
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function summarizeTimesheet(array $context): array
    {
        return $this->handle('timesheet_summary', $context, fn (AiProviderInterface $provider) => $provider->summarizeTimesheet($context));
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function suggestPolicyFixes(array $context): array
    {
        return $this->handle('policy_suggestions', $context, fn (AiProviderInterface $provider) => $provider->suggestPolicyFixes($context));
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function detectAttendanceAnomalies(array $context): array
    {
        return $this->handle('attendance_anomalies', $context, fn (AiProviderInterface $provider) => $provider->detectAttendanceAnomalies($context));
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function generatePersianManagerReport(array $context): array
    {
        return $this->handle('manager_report', $context, fn (AiProviderInterface $provider) => $provider->generatePersianManagerReport($context));
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  callable(AiProviderInterface): array<string, mixed>  $callback
     * @return array<string, mixed>
     */
    private function handle(string $type, array $context, callable $callback): array
    {
        if (! $this->aiEnabled() || ! $this->canUseAi()) {
            return [
                'enabled' => false,
                'message' => 'AI disabled or not permitted.',
            ];
        }

        $provider = $this->resolveProvider();
        $result = $callback($provider);

        $this->log($type, $context, $result, $provider::class);

        return array_merge(['enabled' => true], $result);
    }

    private function aiEnabled(): bool
    {
        return (bool) config('filament-payroll-attendance-ir.ai.enabled', false);
    }

    private function canUseAi(): bool
    {
        return IamAuthorization::allows('payroll.ai.use');
    }

    private function resolveProvider(): AiProviderInterface
    {
        $provider = config('filament-payroll-attendance-ir.ai.provider', \Vendor\FilamentPayrollAttendanceIr\Infrastructure\Ai\FakeAiProvider::class);

        return app($provider);
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $result
     */
    private function log(string $type, array $context, array $result, string $provider): void
    {
        $logPayloads = (bool) config('filament-payroll-attendance-ir.ai.log_payloads', false);
        $inputPayload = $logPayloads ? $context : null;
        $outputPayload = $logPayloads ? $result : null;

        PayrollAiLog::query()->create([
            'company_id' => $context['company_id'] ?? null,
            'actor_id' => auth()->id(),
            'report_type' => $type,
            'period_start' => $context['period_start'] ?? null,
            'period_end' => $context['period_end'] ?? null,
            'provider' => $provider,
            'input_hash' => $this->hashContext($context),
            'response_summary' => $result['report'] ?? ($result['summary'] ?? null),
            'input_payload' => $inputPayload,
            'output_payload' => $outputPayload,
            'metadata' => [
                'ai_enabled' => true,
            ],
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function hashContext(array $context): string
    {
        return hash('sha256', json_encode($context));
    }
}
