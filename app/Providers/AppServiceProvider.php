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
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceOrders\Events\OrderPaid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Haida\FilamentThreeCx\Contracts\ContactDirectoryInterface;
use Haida\FilamentThreeCx\Models\ThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;

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
            $column->formatStateUsing(static function ($state) use ($column, $calendarMode, $formatter, $resolveDateState) {
                $date = $resolveDateState($column, $state);
                if (! $date) {
                    return $state;
                }

                return $formatter->formatForDisplay($date, $calendarMode, $formatter->stateHasTime($state));
            });
        });

        TextEntry::configureUsing(static function (TextEntry $entry) use ($calendarMode, $formatter, $resolveDateState): void {
            $entry->formatStateUsing(static function ($state) use ($entry, $calendarMode, $formatter, $resolveDateState) {
                $date = $resolveDateState($entry, $state);
                if (! $date) {
                    return $state;
                }

                return $formatter->formatForDisplay($date, $calendarMode, $formatter->stateHasTime($state));
            });
        });

        $this->registerCommerceBridges();
    }

    private function registerCommerceBridges(): void
    {
        // Bridge commerce payments into Experience + Loyalty.
        // This must never break checkout; everything is best-effort.
        Event::listen(OrderPaid::class, function (OrderPaid $event): void {
            $order = $event->order;

            $previousTenant = TenantContext::getTenant();
            try {
                $tenant = Tenant::query()->find($order->tenant_id);
                TenantContext::setTenant($tenant);

                $this->bridgeOrderPaidToLoyalty($event);
                $this->bridgeOrderPaidToCsat($event);
                $this->bridgeOrderPaidToCallCenter($event);
            } catch (\Throwable) {
                // Keep checkout resilient.
            } finally {
                TenantContext::setTenant($previousTenant);
            }
        });
    }

    private function bridgeOrderPaidToLoyalty(OrderPaid $event): void
    {
        if (! class_exists(\Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer::class)) {
            return;
        }
        if (! class_exists(\Haida\FilamentLoyaltyClub\Services\LoyaltyEventService::class)) {
            return;
        }

        $order = $event->order;
        if (! $order->user_id) {
            return;
        }

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $userModel */
        $userModel = (string) config('auth.providers.users.model');
        $user = class_exists($userModel) ? $userModel::query()->find($order->user_id) : null;

        $customerName = trim((string) ($order->customer_name ?? ''));
        $firstName = null;
        $lastName = null;
        if ($customerName !== '') {
            $parts = preg_split('/\\s+/u', $customerName) ?: [];
            $firstName = $parts[0] ?? null;
            $lastName = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : null;
        }

        /** @var \Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer $customer */
        $customer = \Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer::query()->firstOrCreate(
            [
                'tenant_id' => (int) $order->tenant_id,
                'user_id' => (int) $order->user_id,
            ],
            [
                'status' => 'active',
                'joined_at' => now(),
                'email' => (string) ($order->customer_email ?? ($user?->email ?? '')) ?: null,
                'phone' => (string) ($order->customer_phone ?? ($user?->phone ?? '')) ?: null,
                'first_name' => $firstName,
                'last_name' => $lastName,
            ],
        );

        try {
            app(\Haida\FilamentLoyaltyClub\Services\LoyaltyEventService::class)->ingest(
                $customer,
                'commerce.order.paid',
                [
                    'order_id' => $order->getKey(),
                    'order_number' => $order->number,
                    'total' => (float) $order->total,
                    'currency' => $order->currency,
                    'paid_at' => $order->paid_at,
                ],
                'commerce.order.paid:'.$order->getKey(),
                'commerce',
            );
        } catch (\Throwable) {
            // Never block checkout.
        }
    }

    private function bridgeOrderPaidToCsat(OrderPaid $event): void
    {
        if (! class_exists(\Haida\FilamentCommerceExperience\Services\CsatSurveyService::class)) {
            return;
        }

        $order = $event->order;

        $channel = null;
        if (! empty($order->customer_email)) {
            $channel = 'email';
        } elseif (! empty($order->customer_phone)) {
            $channel = 'sms';
        } elseif (! empty($order->user_id)) {
            $channel = 'web';
        }

        try {
            app(\Haida\FilamentCommerceExperience\Services\CsatSurveyService::class)->createSurvey([
                'tenant_id' => (int) $order->tenant_id,
                'order_id' => $order->getKey(),
                'customer_id' => $order->user_id ? (int) $order->user_id : null,
                'channel' => $channel,
                'status' => 'sent',
                'metadata' => [
                    'order_number' => $order->number,
                    'total' => (float) $order->total,
                    'currency' => $order->currency,
                ],
            ]);
        } catch (\Throwable) {
            // Never block checkout.
        }
    }

    private function bridgeOrderPaidToCallCenter(OrderPaid $event): void
    {
        if (! interface_exists(ContactDirectoryInterface::class)) {
            return;
        }
        if (! class_exists(ThreeCxInstance::class)) {
            return;
        }

        $order = $event->order;
        $phone = trim((string) ($order->customer_phone ?? ''));
        $email = trim((string) ($order->customer_email ?? ''));
        if ($phone === '' && $email === '') {
            return;
        }

        $instance = ThreeCxInstance::query()
            ->where('tenant_id', $order->tenant_id)
            ->where('crm_connector_enabled', true)
            ->orderByDesc('id')
            ->first();

        if (! $instance) {
            return;
        }

        $displayName = trim((string) ($order->customer_name ?? ''));
        if ($displayName === '') {
            $displayName = $email !== '' ? $email : $phone;
        }

        try {
            // Deduplicate by phone/email if possible.
            $existing = null;
            if ($phone !== '' && class_exists(ThreeCxContact::class)) {
                $existing = ThreeCxContact::query()
                    ->where('instance_id', $instance->getKey())
                    ->whereJsonContains('phones', $phone)
                    ->first();
            }
            if (! $existing && $email !== '' && class_exists(ThreeCxContact::class)) {
                $existing = ThreeCxContact::query()
                    ->where('instance_id', $instance->getKey())
                    ->whereJsonContains('emails', $email)
                    ->first();
            }

            if ($existing) {
                $existing->update([
                    'name' => $displayName,
                ]);
                return;
            }

            $payload = [
                'name' => $displayName,
                'phones' => $phone !== '' ? [$phone] : [],
                'emails' => $email !== '' ? [$email] : [],
                'external_id' => 'order:'.$order->getKey(),
                'crm_url' => null,
            ];

            app(ContactDirectoryInterface::class)->create($instance, $payload);
        } catch (\Throwable) {
            // Never block checkout.
        }
    }
}
