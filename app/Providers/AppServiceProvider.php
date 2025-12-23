<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use App\Support\Calendar\CalendarFormatter;
use Carbon\Carbon;
use DateTimeInterface;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));

        $settings = null;
        try {
            if (Schema::hasTable('settings')) {
                $settings = app(GeneralSettings::class);
            }
        } catch (\Throwable) {
            $settings = null;
        }

        $calendarMode = $settings?->calendar_display_mode ?? 'jalali';
        if (! in_array($calendarMode, ['jalali', 'gregorian', 'hijri'], true)) {
            $calendarMode = 'jalali';
        }

        $formatter = app(CalendarFormatter::class);

        if (! DatePicker::hasMacro('hijri')) {
            DatePicker::macro('hijri', function (): DatePicker {
                /** @var DatePicker $this */
                $this->view = 'filament-hijri-picker::components.hijri-date-time-picker';
                $this->displayFormat('Y/m/d');
                $this->firstDayOfWeek(6);

                return $this;
            });
        }

        if (! DateTimePicker::hasMacro('hijri')) {
            DateTimePicker::macro('hijri', function (): DateTimePicker {
                /** @var DateTimePicker $this */
                $this->view = 'filament-hijri-picker::components.hijri-date-time-picker';
                $this->displayFormat('Y/m/d H:i');
                $this->firstDayOfWeek(6);

                return $this;
            });
        }

        DatePicker::configureUsing(static function (DatePicker $component) use ($calendarMode): void {
            if ($calendarMode === 'jalali' && DatePicker::hasMacro('jalali')) {
                $component->jalali(weekdaysShort: true)->hasToday();
                return;
            }

            if ($calendarMode === 'hijri' && DatePicker::hasMacro('hijri')) {
                $component->hijri();
            }
        });

        DateTimePicker::configureUsing(static function (DateTimePicker $component) use ($calendarMode): void {
            if ($calendarMode === 'jalali' && DateTimePicker::hasMacro('jalali')) {
                $component->jalali(weekdaysShort: true)->hasToday();
                return;
            }

            if ($calendarMode === 'hijri' && DateTimePicker::hasMacro('hijri')) {
                $component->hijri();
            }
        });

        $resolveDateState = static function (object $component, mixed $state) use ($formatter): ?Carbon {
            if ($state instanceof DateTimeInterface) {
                return Carbon::instance($state);
            }

            $name = method_exists($component, 'getName') ? $component->getName() : null;
            $record = method_exists($component, 'getRecord') ? $component->getRecord() : null;
            $attribute = is_string($name) ? Str::afterLast($name, '.') : null;

            if ($record instanceof Model && is_string($attribute)) {
                if (in_array($attribute, ['created_at', 'updated_at', 'deleted_at'], true)) {
                    return $formatter->parseToCarbon($state);
                }

                if ($record->hasCast($attribute, ['date', 'datetime', 'immutable_date', 'immutable_datetime'])) {
                    return $formatter->parseToCarbon($state);
                }

                if (method_exists($record, 'getDates') && in_array($attribute, $record->getDates(), true)) {
                    return $formatter->parseToCarbon($state);
                }
            }

            if (is_string($state) && $formatter->looksLikeDateString($state)) {
                return $formatter->parseToCarbon($state);
            }

            return null;
        };

        TextColumn::configureUsing(static function (TextColumn $column) use ($calendarMode, $formatter, $resolveDateState): void {
            $column->formatStateUsing(static function (TextColumn $column, $state) use ($calendarMode, $formatter, $resolveDateState) {
                $date = $resolveDateState($column, $state);
                if (! $date) {
                    return $state;
                }

                return $formatter->formatForDisplay($date, $calendarMode, $formatter->stateHasTime($state));
            });
        });

        TextEntry::configureUsing(static function (TextEntry $entry) use ($calendarMode, $formatter, $resolveDateState): void {
            $entry->formatStateUsing(static function (TextEntry $entry, $state) use ($calendarMode, $formatter, $resolveDateState) {
                $date = $resolveDateState($entry, $state);
                if (! $date) {
                    return $state;
                }

                return $formatter->formatForDisplay($date, $calendarMode, $formatter->stateHasTime($state));
            });
        });
    }
}
