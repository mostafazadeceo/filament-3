<?php

namespace App\Support\Calendar;

use Ariaieboy\Jalali\Jalali;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTimeInterface;
use GeniusTS\HijriDate\Date as HijriDate;
use GeniusTS\HijriDate\Hijri;
use GeniusTS\HijriDate\Translations\Persian as HijriPersian;

class CalendarFormatter
{
    private const PERSIAN_DIGITS = [
        '0' => '۰',
        '1' => '۱',
        '2' => '۲',
        '3' => '۳',
        '4' => '۴',
        '5' => '۵',
        '6' => '۶',
        '7' => '۷',
        '8' => '۸',
        '9' => '۹',
    ];

    private static bool $hijriConfigured = false;

    public function formatDayDate(CarbonInterface $date, string $calendar): string
    {
        $date = $this->toDisplayTimezone($date);
        $calendar = $this->normalizeCalendar($calendar);

        $formatted = match ($calendar) {
            'jalali' => Jalali::fromCarbon($date)->format('l j F Y'),
            'hijri' => $this->formatHijri($date, 'l j F Y'),
            default => $this->formatGregorian($date, 'l j F Y'),
        };

        return $this->normalizeDigits($formatted);
    }

    public function formatDate(CarbonInterface $date, string $calendar): string
    {
        $date = $this->toDisplayTimezone($date);
        $calendar = $this->normalizeCalendar($calendar);

        $formatted = match ($calendar) {
            'jalali' => Jalali::fromCarbon($date)->format('Y/m/d'),
            'hijri' => $this->formatHijri($date, 'Y/m/d'),
            default => $this->formatGregorian($date, 'Y/m/d'),
        };

        return $this->normalizeDigits($formatted);
    }

    public function formatDateTime(CarbonInterface $date, string $calendar): string
    {
        $date = $this->toDisplayTimezone($date);
        $calendar = $this->normalizeCalendar($calendar);

        $formatted = match ($calendar) {
            'jalali' => Jalali::fromCarbon($date)->format('Y/m/d H:i'),
            'hijri' => $this->formatHijri($date, 'Y/m/d H:i'),
            default => $this->formatGregorian($date, 'Y/m/d H:i'),
        };

        return $this->normalizeDigits($formatted);
    }

    public function formatForDisplay(CarbonInterface $date, string $calendar, bool $includeTime = false): string
    {
        return $includeTime
            ? $this->formatDateTime($date, $calendar)
            : $this->formatDate($date, $calendar);
    }

    public function parseToCarbon(mixed $state): ?CarbonInterface
    {
        if ($state instanceof CarbonInterface) {
            return $state;
        }

        if ($state instanceof DateTimeInterface) {
            return Carbon::instance($state);
        }

        if (! is_string($state)) {
            return null;
        }

        if (! $this->looksLikeDateString($state)) {
            return null;
        }

        try {
            return Carbon::parse($state);
        } catch (\Throwable) {
            return null;
        }
    }

    public function stateHasTime(mixed $state): bool
    {
        if ($state instanceof CarbonInterface) {
            return $state->format('H:i:s') !== '00:00:00';
        }

        if ($state instanceof DateTimeInterface) {
            return Carbon::instance($state)->format('H:i:s') !== '00:00:00';
        }

        if (is_string($state)) {
            try {
                return Carbon::parse($state)->format('H:i:s') !== '00:00:00';
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }

    public function looksLikeDateString(string $value): bool
    {
        return (bool) preg_match('/^\d{4}[-\/]\d{1,2}[-\/]\d{1,2}/', $value)
            || (bool) preg_match('/^\d{4}-\d{2}-\d{2}T/', $value);
    }

    private function formatHijri(CarbonInterface $date, string $format): string
    {
        $this->configureHijri();

        return Hijri::convertToHijri(Carbon::instance($date))->format($format);
    }

    private function formatGregorian(CarbonInterface $date, string $format): string
    {
        return Carbon::instance($date)
            ->locale(config('app.locale'))
            ->translatedFormat($format);
    }

    private function toDisplayTimezone(CarbonInterface $date): CarbonInterface
    {
        $timezone = $this->resolveDisplayTimezone();
        $date = Carbon::instance($date);

        if (! $timezone) {
            return $date;
        }

        return $date->setTimezone($timezone);
    }

    private function resolveDisplayTimezone(): ?string
    {
        $timezone = config('app.display_timezone');

        if (! is_string($timezone) || $timezone === '') {
            return null;
        }

        return $timezone;
    }

    private function normalizeCalendar(string $calendar): string
    {
        $calendar = strtolower($calendar);

        return match ($calendar) {
            'jalali', 'hijri', 'gregorian' => $calendar,
            default => 'jalali',
        };
    }

    private function normalizeDigits(string $value): string
    {
        if (config('app.locale') !== 'fa') {
            return $value;
        }

        return strtr($value, self::PERSIAN_DIGITS);
    }

    private function configureHijri(): void
    {
        if (self::$hijriConfigured) {
            return;
        }

        HijriDate::setTranslation(new HijriPersian());
        HijriDate::setDefaultNumbers(HijriDate::PERSIAN_NUMBERS);
        HijriDate::setToStringFormat('l j F Y');

        self::$hijriConfigured = true;
    }
}
