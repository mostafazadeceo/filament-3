<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Ai;

interface AiProviderInterface
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function summarizeTimesheet(array $context): array;

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function suggestPolicyFixes(array $context): array;

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function detectAttendanceAnomalies(array $context): array;

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function generatePersianManagerReport(array $context): array;
}
