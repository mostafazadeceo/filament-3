<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Services;

use Haida\FilamentMailOps\Models\MailDomain;

class DomainDnsAuditService
{
    /**
     * @var array<string, array{label: string, critical: bool}>
     */
    private const CHECKS = [
        'dns_mx' => ['label' => 'MX', 'critical' => true],
        'dns_spf' => ['label' => 'SPF', 'critical' => true],
        'dns_dkim' => ['label' => 'DKIM', 'critical' => true],
        'dns_dmarc' => ['label' => 'DMARC', 'critical' => true],
        'dns_dmarc_report' => ['label' => 'DMARC Report', 'critical' => false],
        'dns_tlsa' => ['label' => 'TLSA', 'critical' => false],
        'dns_autoconfig' => ['label' => 'Autoconfig', 'critical' => false],
    ];

    /**
     * @return array{
     *     status: string,
     *     score: int,
     *     checks: array<int, array{key: string, label: string, critical: bool, expected: string|null, configured: bool, status: string}>,
     *     issues: array<int, string>,
     *     checked_at: string
     * }
     */
    public function evaluate(?array $snapshot): array
    {
        $snapshot = is_array($snapshot) ? $snapshot : [];

        $checks = [];
        $issues = [];
        $criticalTotal = 0;
        $criticalOk = 0;
        $optionalTotal = 0;
        $optionalOk = 0;

        foreach (self::CHECKS as $key => $meta) {
            $expected = $this->normalizeValue($snapshot[$key] ?? null);
            $configured = $this->isConfigured($expected);
            $critical = $meta['critical'];

            $checks[] = [
                'key' => $key,
                'label' => $meta['label'],
                'critical' => $critical,
                'expected' => $expected,
                'configured' => $configured,
                'status' => $configured ? 'ok' : 'missing',
            ];

            if ($critical) {
                $criticalTotal++;
                if ($configured) {
                    $criticalOk++;
                } else {
                    $issues[] = "رکورد {$meta['label']} تنظیم نشده است.";
                }
            } else {
                $optionalTotal++;
                if ($configured) {
                    $optionalOk++;
                }
            }
        }

        if ($criticalOk === 0 && $optionalOk === 0) {
            return [
                'status' => 'unknown',
                'score' => 0,
                'checks' => $checks,
                'issues' => ['هنوز هیچ رکورد DNS در snapshot ثبت نشده است.'],
                'checked_at' => now()->toIso8601String(),
            ];
        }

        $criticalRatio = $criticalTotal > 0 ? $criticalOk / $criticalTotal : 1;
        $optionalRatio = $optionalTotal > 0 ? $optionalOk / $optionalTotal : 1;
        $score = (int) round(($criticalRatio * 80) + ($optionalRatio * 20));

        $status = match (true) {
            $criticalOk === $criticalTotal && $score >= 95 => 'healthy',
            $criticalOk >= max(1, $criticalTotal - 1) => 'warning',
            default => 'critical',
        };

        return [
            'status' => $status,
            'score' => $score,
            'checks' => $checks,
            'issues' => $issues,
            'checked_at' => now()->toIso8601String(),
        ];
    }

    public function applyToDomain(MailDomain $domain): MailDomain
    {
        $audit = $this->evaluate($domain->dns_snapshot);

        $domain->update([
            'dns_health_status' => $audit['status'],
            'dns_health_score' => $audit['score'],
            'dns_issues' => $audit['issues'],
            'dns_last_checked_at' => now(),
        ]);

        return $domain->refresh();
    }

    public function recordsAsText(?array $snapshot): string
    {
        $audit = $this->evaluate($snapshot);

        $lines = [
            'Mail DNS Checklist',
            '==================',
            'Health: '.$this->healthLabel($audit['status']).' ('.$audit['score'].'%)',
            '',
        ];

        foreach ($audit['checks'] as $check) {
            $lines[] = sprintf(
                '[%s] %s%s',
                $check['configured'] ? 'OK' : 'MISSING',
                $check['label'],
                $check['critical'] ? ' (critical)' : ''
            );
            $lines[] = 'Expected: '.($check['expected'] ?: 'N/A');
            $lines[] = '';
        }

        if ($audit['issues'] !== []) {
            $lines[] = 'Issues:';
            foreach ($audit['issues'] as $issue) {
                $lines[] = '- '.$issue;
            }
        }

        return implode(PHP_EOL, $lines);
    }

    public function healthLabel(?string $status): string
    {
        return match ($status) {
            'healthy' => 'سالم',
            'warning' => 'نیازمند بهبود',
            'critical' => 'بحرانی',
            default => 'نامشخص',
        };
    }

    private function normalizeValue(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);

            return $trimmed !== '' ? $trimmed : null;
        }

        if (is_bool($value)) {
            return $value ? 'true' : null;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_array($value)) {
            $parts = $this->flattenStrings($value);

            return $parts !== [] ? implode(PHP_EOL, $parts) : null;
        }

        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if (! is_string($encoded) || $encoded === 'null' || trim($encoded) === '') {
            return null;
        }

        return $encoded;
    }

    private function isConfigured(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $normalized = strtolower(trim($value));

        return ! in_array($normalized, ['', '-', 'false', 'null', 'none', 'n/a', '0'], true);
    }

    /**
     * @param  array<int|string, mixed>  $value
     * @return array<int, string>
     */
    private function flattenStrings(array $value): array
    {
        $result = [];

        foreach ($value as $item) {
            if (is_array($item)) {
                $result = array_merge($result, $this->flattenStrings($item));

                continue;
            }

            if ($item === null || $item === false) {
                continue;
            }

            $string = trim((string) $item);
            if ($string !== '') {
                $result[] = $string;
            }
        }

        return array_values(array_unique($result));
    }
}
