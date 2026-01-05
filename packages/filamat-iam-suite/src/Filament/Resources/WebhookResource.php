<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\WebhookResource\Pages\CreateWebhook;
use Filamat\IamSuite\Filament\Resources\WebhookResource\Pages\EditWebhook;
use Filamat\IamSuite\Filament\Resources\WebhookResource\Pages\ListWebhooks;
use Filamat\IamSuite\Filament\Resources\WebhookResource\RelationManagers\WebhookDeliveriesRelationManager;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Services\WebhookService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\N8nEventCatalog;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class WebhookResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'api';

    protected static ?string $model = Webhook::class;

    protected static ?string $navigationLabel = 'وبهوک‌ها';

    protected static ?string $pluralModelLabel = 'وبهوک‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(required: false)
                    ->required(fn (Get $get): bool => $get('type') === 'automation'),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'notifications' => 'اعلان‌ها',
                        'payments' => 'پرداخت',
                        'subscriptions' => 'اشتراک',
                        'security' => 'امنیت',
                        'workhub' => 'رهگیری کارها',
                        'accounting' => 'حسابداری',
                        'automation' => 'اتوماسیون (n8n)',
                    ])
                    ->helperText('انتخاب نوع، فهرست رویدادهای قابل فیلتر را مشخص می‌کند.')
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state === 'automation') {
                            $set('events', N8nEventCatalog::defaultSubscriptions());

                            return;
                        }

                        $set('events', []);
                    })
                    ->required(),
                Select::make('events')
                    ->label('رویدادها')
                    ->multiple()
                    ->options(fn (Get $get): array => self::eventOptions($get('type')))
                    ->helperText('اگر رویدادی انتخاب نشود، همه رخدادهای این نوع ارسال می‌شوند.')
                    ->disabled(fn (Get $get): bool => empty(self::eventOptions($get('type')))),
                TextInput::make('url')->label('نشانی وب')->url()->required(),
                TextInput::make('secret')
                    ->label('کلید امضا/توکن')
                    ->password()
                    ->revealable()
                    ->default(fn () => Str::random(32))
                    ->required(),
                Select::make('auth_mode')
                    ->label('روش احراز')
                    ->options([
                        'hmac+nonce' => 'HMAC + Nonce',
                        'header' => 'هدر ثابت',
                        'basic' => 'Basic Auth',
                        'jwt' => 'Bearer Token',
                        'none' => 'بدون احراز',
                    ])
                    ->default(fn () => config('filamat-iam.automation.default_auth_mode', 'hmac+nonce'))
                    ->visible(fn (Get $get): bool => $get('type') === 'automation'),
                KeyValue::make('headers_static')
                    ->label('هدرهای ثابت')
                    ->helperText('برای auth_mode=header می‌توانید کلید auth_header را تعیین کنید.')
                    ->visible(fn (Get $get): bool => $get('type') === 'automation'),
                KeyValue::make('redaction_policy')
                    ->label('سیاست پالایش داده')
                    ->helperText('مثال: actor.ip=remove یا actor.email=mask')
                    ->visible(fn (Get $get): bool => $get('type') === 'automation'),
                TextInput::make('rate_limit.max_attempts')
                    ->label('حداکثر ارسال در بازه')
                    ->numeric()
                    ->default(fn () => (int) config('filamat-iam.automation.rate_limit.max_attempts', 60))
                    ->visible(fn (Get $get): bool => $get('type') === 'automation'),
                TextInput::make('rate_limit.decay_seconds')
                    ->label('بازه زمانی (ثانیه)')
                    ->numeric()
                    ->default(fn () => (int) config('filamat-iam.automation.rate_limit.decay_seconds', 60))
                    ->visible(fn (Get $get): bool => $get('type') === 'automation'),
                Toggle::make('is_ai_auditor')
                    ->label('کانال حسابرسی هوشمند')
                    ->helperText('در اجرای زمان‌بندی حسابرسی، فقط این وبهوک‌ها انتخاب می‌شوند.')
                    ->visible(fn (Get $get): bool => $get('type') === 'automation'),
                Toggle::make('enabled')->label('فعال')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'notifications' => 'اعلان‌ها',
                        'payments' => 'پرداخت',
                        'subscriptions' => 'اشتراک',
                        'security' => 'امنیت',
                        'workhub' => 'رهگیری کارها',
                        'accounting' => 'حسابداری',
                        'automation' => 'اتوماسیون (n8n)',
                        default => $state,
                    }),
                TextColumn::make('url')->label('نشانی وب')->limit(40),
                IconColumn::make('enabled')->label('فعال')->boolean(),
                IconColumn::make('is_ai_auditor')
                    ->label('حسابرسی هوشمند')
                    ->boolean()
                    ->visible(fn (?Webhook $record) => $record?->type === 'automation'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                Action::make('test')
                    ->label('تست')
                    ->visible(fn () => IamAuthorization::allows('api.manage'))
                    ->action(function (Webhook $record) {
                        app(WebhookService::class)->queue($record, [
                            'event' => 'test',
                            'message' => 'پیام تست وبهوک',
                            'timestamp' => now()->toIso8601String(),
                        ]);
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            WebhookDeliveriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWebhooks::route('/'),
            'create' => CreateWebhook::route('/create'),
            'edit' => EditWebhook::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected static function eventOptions(?string $type): array
    {
        return match ($type) {
            'accounting' => [
                'journal_entry.posted' => 'ثبت قطعی سند حسابداری',
                'sales_invoice.posted' => 'ثبت قطعی فاکتور فروش',
                'purchase_invoice.posted' => 'ثبت قطعی فاکتور خرید',
                'treasury_transaction.posted' => 'ثبت قطعی تراکنش خزانه',
                'inventory_doc.posted' => 'ثبت سند انبار',
                'vat_report.submitted' => 'ارسال گزارش ارزش افزوده',
                'e_invoice.sent' => 'ارسال صورتحساب الکترونیکی',
                'e_invoice.failed' => 'خطا در ارسال صورتحساب الکترونیکی',
            ],
            'workhub' => [
                'project.created' => 'ایجاد پروژه',
                'project.updated' => 'ویرایش پروژه',
                'work_item.created' => 'ایجاد آیتم کاری',
                'work_item.updated' => 'ویرایش آیتم کاری',
                'work_item.transitioned' => 'انتقال آیتم کاری',
                'comment.created' => 'ایجاد دیدگاه',
                'attachment.created' => 'ایجاد پیوست',
            ],
            'automation' => N8nEventCatalog::options(),
            default => [],
        };
    }
}
