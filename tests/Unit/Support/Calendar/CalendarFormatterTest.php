<?php

namespace Tests\Unit\Support\Calendar;

use App\Support\Calendar\CalendarFormatter;
use Carbon\Carbon;
use Tests\TestCase;

class CalendarFormatterTest extends TestCase
{
    public function test_formats_jalali_with_persian_digits(): void
    {
        config(['app.locale' => 'fa']);

        $formatter = app(CalendarFormatter::class);
        $date = Carbon::create(2025, 1, 1, 0, 0, 0, 'UTC');

        $formatted = $formatter->formatDate($date, 'jalali');

        $this->assertMatchesRegularExpression('/[۰-۹]/u', $formatted);
        $this->assertDoesNotMatchRegularExpression('/[0-9]/', $formatted);
    }

    public function test_applies_display_timezone_when_formatting(): void
    {
        config([
            'app.locale' => 'en',
            'app.display_timezone' => 'Asia/Tehran',
        ]);

        $formatter = app(CalendarFormatter::class);
        $date = Carbon::create(2025, 1, 1, 0, 0, 0, 'UTC');

        $formatted = $formatter->formatDateTime($date, 'gregorian');

        $this->assertStringContainsString('03:30', $formatted);
    }
}
