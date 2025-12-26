<?php

namespace Haida\FilamentCurrencyRates\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Haida\FilamentCurrencyRates\Jobs\SyncCurrencyRatesJob;
use Haida\FilamentCurrencyRates\Models\CurrencyRateRun;
use Haida\FilamentCurrencyRates\Settings\CurrencyRateSettings;
use Haida\FilamentCurrencyRates\Support\CurrencyUnit;
use Illuminate\Support\Str;

class CurrencyRateSettingsPage extends SettingsPage
{
    protected static string $settings = CurrencyRateSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'تنظیمات نرخ ارز';

    protected static ?string $title = 'تنظیمات نرخ ارز';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'settings/currency-rates';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync')
                ->label('همگام‌سازی الان')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    SyncCurrencyRatesJob::dispatch();
                    \Filament\Notifications\Notification::make()
                        ->title('همگام‌سازی در صف قرار گرفت.')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function form(Schema $schema): Schema
    {
        $unitOptions = [
            CurrencyUnit::IRT => 'تومان',
            CurrencyUnit::IRR => 'ریال',
        ];

        return $schema
            ->components([
                Section::make('وضعیت و زمان‌بندی')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('فعال‌سازی همگام‌سازی')
                            ->helperText('اگر خاموش باشد، نرخ‌ها به‌روزرسانی نمی‌شوند.')
                            ->live(),
                        TextInput::make('interval_minutes')
                            ->label('تایم رفرش (دقیقه)')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(60)
                            ->helperText('حداقل 5 و حداکثر 60 دقیقه.')
                            ->required(),
                        TextInput::make('last_sync')
                            ->label('آخرین همگام‌سازی')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (TextInput $component): void {
                                $last = CurrencyRateRun::query()->latest('fetched_at')->value('fetched_at');
                                if (! $last) {
                                    $component->state('-');

                                    return;
                                }

                                $date = \Carbon\Carbon::parse($last);
                                if (class_exists(\App\Support\Calendar\CalendarFormatter::class)) {
                                    $formatter = app(\App\Support\Calendar\CalendarFormatter::class);
                                    $component->state($formatter->formatDateTime($date, 'jalali'));

                                    return;
                                }

                                if (class_exists(\Ariaieboy\Jalali\Jalali::class)) {
                                    $component->state(\Ariaieboy\Jalali\Jalali::fromCarbon($date)->format('Y/m/d H:i'));

                                    return;
                                }

                                $component->state($date->format('Y-m-d H:i'));
                            }),
                    ])
                    ->columns(3),
                Section::make('منبع داده')
                    ->schema([
                        Select::make('source')
                            ->label('منبع')
                            ->options([
                                'alanchand' => 'اسکرپ از الان‌چند',
                                'custom_api' => 'ای‌پی‌آی سفارشی',
                            ])
                            ->required(),
                        TextInput::make('scrape_url')
                            ->label('نشانی صفحه اسکرپ')
                            ->placeholder('https://alanchand.com/')
                            ->visible(fn (Get $get): bool => $get('source') === 'alanchand')
                            ->required(fn (Get $get): bool => $get('source') === 'alanchand')
                            ->dehydratedWhenHidden()
                            ->maxLength(2048),
                        TextInput::make('custom_api_url')
                            ->label('نشانی ای‌پی‌آی سفارشی')
                            ->visible(fn (Get $get): bool => $get('source') === 'custom_api')
                            ->required(fn (Get $get): bool => $get('source') === 'custom_api')
                            ->dehydratedWhenHidden()
                            ->maxLength(2048),
                        TextInput::make('custom_api_token')
                            ->label('توکن ای‌پی‌آی سفارشی')
                            ->password()
                            ->revealable()
                            ->visible(fn (Get $get): bool => $get('source') === 'custom_api')
                            ->helperText('اگر ای‌پی‌آی شما نیاز به احراز هویت دارد وارد کنید.')
                            ->dehydratedWhenHidden()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('واحد قیمت‌ها')
                    ->schema([
                        Select::make('source_unit')
                            ->label('واحد داده منبع')
                            ->options($unitOptions)
                            ->helperText('برای الان‌چند معمولاً تومان است. اگر منبع شما ریال می‌دهد، ریال را انتخاب کنید.')
                            ->required(),
                        Select::make('display_unit')
                            ->label('واحد نمایش در پنل و خروجی')
                            ->options($unitOptions)
                            ->helperText('تمام قیمت‌ها در پنل و ای‌پی‌آی با این واحد نمایش داده می‌شوند.')
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('قیمت‌گذاری')
                    ->schema([
                        Select::make('base_rate')
                            ->label('مبنای نرخ')
                            ->options([
                                'sell' => 'قیمت فروش',
                                'buy' => 'قیمت خرید',
                                'average' => 'میانگین خرید و فروش',
                            ])
                            ->helperText('تبدیل قیمت‌ها و محاسبات بر اساس این مبنا انجام می‌شود.')
                            ->required(),
                        Toggle::make('profit_enabled')
                            ->label('فعال‌سازی سود')
                            ->helperText('اگر روشن باشد، سود روی نرخ‌ها اعمال می‌شود.')
                            ->live(),
                        TextInput::make('profit_percent')
                            ->label('سود درصدی')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(500)
                            ->suffix('%')
                            ->visible(fn (Get $get): bool => (bool) $get('profit_enabled'))
                            ->dehydratedWhenHidden()
                            ->helperText('می‌توانید همزمان با سود ثابت نیز استفاده کنید.'),
                        TextInput::make('profit_fixed_amount')
                            ->label('سود ثابت')
                            ->numeric()
                            ->minValue(0)
                            ->visible(fn (Get $get): bool => (bool) $get('profit_enabled'))
                            ->dehydratedWhenHidden(),
                        Select::make('profit_fixed_unit')
                            ->label('واحد سود ثابت')
                            ->options($unitOptions)
                            ->visible(fn (Get $get): bool => (bool) $get('profit_enabled'))
                            ->dehydratedWhenHidden()
                            ->required(fn (Get $get): bool => (bool) $get('profit_enabled')),
                    ])
                    ->columns(2),
                Section::make('ارزهای مورد نیاز')
                    ->schema([
                        TagsInput::make('currencies')
                            ->label('کد ارزها')
                            ->helperText('نمونه: usd, eur, gbp, aed, cny')
                            ->suggestions(['usd', 'eur', 'gbp', 'aed', 'cny'])
                            ->dehydrateStateUsing(function ($state) {
                                return collect($state)
                                    ->filter()
                                    ->map(fn ($code) => Str::lower(trim((string) $code)))
                                    ->unique()
                                    ->values()
                                    ->all();
                            })
                            ->required(),
                    ]),
                Section::make('تنظیمات ای‌پی‌آی خروجی')
                    ->schema([
                        Toggle::make('api_enabled')
                            ->label('فعال‌سازی ای‌پی‌آی')
                            ->helperText('خروجی نرخ‌ها از مسیر ای‌پی‌آی قابل دریافت است.')
                            ->live(),
                        TextInput::make('api_token')
                            ->label('توکن دسترسی ای‌پی‌آی')
                            ->password()
                            ->revealable()
                            ->helperText('اگر پر شود، باید در هدر X-Rate-Token ارسال شود.')
                            ->visible(fn (Get $get): bool => (bool) $get('api_enabled'))
                            ->dehydratedWhenHidden()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('پیشرفته')
                    ->schema([
                        Toggle::make('cache_enabled')
                            ->label('فعالسازی کش')
                            ->helperText('برای کاهش درخواست به منبع.')
                            ->live(),
                        TextInput::make('cache_ttl_seconds')
                            ->label('مدت کش (ثانیه)')
                            ->numeric()
                            ->minValue(60)
                            ->helperText('حداقل 60 ثانیه.')
                            ->required(),
                        TextInput::make('timeout')
                            ->label('مهلت درخواست (ثانیه)')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(60)
                            ->required(),
                        TextInput::make('retry_times')
                            ->label('تعداد تلاش مجدد')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5)
                            ->required(),
                        TextInput::make('retry_sleep_ms')
                            ->label('وقفه بین تلاش‌ها (میلی‌ثانیه)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(5000)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
