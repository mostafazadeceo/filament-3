<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Ai;

class FakeAiProvider implements AiProviderInterface
{
    public function summarizeTimesheet(array $context): array
    {
        return [
            'summary' => 'خلاصه دوره در حال حاضر توسط ارائه‌دهنده آزمایشی تولید شد.',
            'anomalies' => [],
        ];
    }

    public function suggestPolicyFixes(array $context): array
    {
        return [
            'suggestions' => [
                'بازنگری در قواعد اضافه‌کار برای کاهش استثناها.',
            ],
        ];
    }

    public function detectAttendanceAnomalies(array $context): array
    {
        return [
            'anomalies' => [],
        ];
    }

    public function generatePersianManagerReport(array $context): array
    {
        return [
            'report' => 'گزارش مدیریتی آزمایشی: روند کلی حضور و غیاب پایدار است و نیاز به بازبینی دستی محدود است.',
            'highlights' => [
                'اضافه‌کار در محدوده مجاز ثبت شده است.',
                'مورد بحرانی ثبت نشده است.',
            ],
        ];
    }
}
