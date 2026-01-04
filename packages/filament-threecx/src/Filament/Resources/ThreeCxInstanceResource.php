<?php

namespace Haida\FilamentThreeCx\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource\Pages\CreateThreeCxInstance;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource\Pages\EditThreeCxInstance;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxInstanceResource\Pages\ListThreeCxInstances;
use Haida\FilamentThreeCx\Jobs\SyncCallHistoryJob;
use Haida\FilamentThreeCx\Jobs\SyncChatHistoryJob;
use Haida\FilamentThreeCx\Jobs\SyncContactsJob;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Services\ThreeCxCapabilityDetector;
use Haida\FilamentThreeCx\Services\ThreeCxEventDispatcher;
use Haida\FilamentThreeCx\Support\ThreeCxNotifier;

class ThreeCxInstanceResource extends Resource
{
    protected static ?string $model = ThreeCxInstance::class;

    protected static ?string $modelLabel = 'اتصال 3CX';

    protected static ?string $pluralModelLabel = 'اتصال‌های 3CX';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'اتصال‌ها';

    protected static string|\UnitEnum|null $navigationGroup = '3CX';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allows('threecx.view');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('اتصال')
                    ->schema([
                        TextInput::make('name')
                            ->label('نام')
                            ->required()
                            ->maxLength(150),
                        TextInput::make('base_url')
                            ->label('نشانی پایه')
                            ->required()
                            ->maxLength(255),
                        Toggle::make('verify_tls')
                            ->label('اعتبارسنجی TLS')
                            ->default(true),
                        TextInput::make('client_id')
                            ->label('Client ID')
                            ->password()
                            ->revealable()
                            ->helperText('برای حفظ مقدار فعلی خالی بگذارید.')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->disabled(fn (): bool => ! IamAuthorization::allows('threecx.manage')),
                        TextInput::make('client_secret')
                            ->label('Client Secret')
                            ->password()
                            ->revealable()
                            ->helperText('برای حفظ مقدار فعلی خالی بگذارید.')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->disabled(fn (): bool => ! IamAuthorization::allows('threecx.manage')),
                    ])
                    ->columns(2),
                Section::make('دسترسی‌ها')
                    ->schema([
                        Toggle::make('xapi_enabled')
                            ->label('فعال‌سازی XAPI')
                            ->default((bool) config('filament-threecx.features.xapi_enabled', true)),
                        Toggle::make('call_control_enabled')
                            ->label('فعال‌سازی Call Control')
                            ->default((bool) config('filament-threecx.features.call_control_enabled', false)),
                        Toggle::make('crm_connector_enabled')
                            ->label('فعال‌سازی اتصال CRM')
                            ->default((bool) config('filament-threecx.features.crm_connector_enabled', false)),
                        TextInput::make('crm_connector_key')
                            ->label('کلید اتصال CRM')
                            ->password()
                            ->revealable()
                            ->helperText('برای حفظ کلید فعلی خالی بگذارید.')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->disabled(fn (): bool => ! IamAuthorization::allows('threecx.manage')),
                    ])
                    ->columns(2),
                Section::make('تنظیمات تکمیلی')
                    ->schema([
                        TextInput::make('route_point_dn')
                            ->label('Route Point DN')
                            ->maxLength(120),
                        TagsInput::make('monitored_dns')
                            ->label('DNهای مانیتور')
                            ->placeholder('1001')
                            ->helperText('شماره‌های داخلی را به صورت تگ وارد کنید.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('وضعیت')
                    ->schema([
                        TextInput::make('last_version')
                            ->label('نسخه')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('last_health_at')
                            ->label('آخرین سلامت')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('last_error')
                            ->label('آخرین خطا')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('base_url')->label('نشانی پایه')->toggleable(),
                IconColumn::make('xapi_enabled')->label('XAPI')->boolean(),
                IconColumn::make('call_control_enabled')->label('Call Control')->boolean(),
                IconColumn::make('crm_connector_enabled')->label('CRM')->boolean(),
                TextColumn::make('last_health_at')->label('آخرین سلامت')->jalaliDateTime()->sortable(),
                TextColumn::make('updated_at')->label('به‌روزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                Action::make('health')
                    ->label('تست اتصال')
                    ->icon('heroicon-o-beaker')
                    ->visible(fn (ThreeCxInstance $record) => $record->xapi_enabled && IamAuthorization::allows('threecx.sync'))
                    ->action(function (ThreeCxInstance $record, ThreeCxEventDispatcher $events) {
                        try {
                            $client = app(XapiClient::class, ['instance' => $record]);
                            $payload = $client->health();
                            $record->update([
                                'last_health_at' => now(),
                                'last_error' => null,
                                'last_version' => $payload['version'] ?? $record->last_version,
                            ]);

                            ThreeCxNotifier::success('اتصال موفق بود.');
                        } catch (\Throwable $exception) {
                            $record->update(['last_error' => $exception->getMessage()]);
                            $events->dispatchHealthDegraded($record, $exception->getMessage());
                            ThreeCxNotifier::error($exception, 'آزمون اتصال ناموفق بود.');
                        }
                    }),
                Action::make('capabilities')
                    ->label('دریافت قابلیت‌ها')
                    ->icon('heroicon-o-sparkles')
                    ->visible(fn (ThreeCxInstance $record) => $record->xapi_enabled && IamAuthorization::allows('threecx.sync'))
                    ->action(function (ThreeCxInstance $record, ThreeCxCapabilityDetector $detector) {
                        try {
                            $capabilities = $detector->detect($record);
                            $record->update([
                                'last_capabilities_json' => $capabilities,
                                'last_health_at' => now(),
                                'last_error' => null,
                            ]);

                            ThreeCxNotifier::success('قابلیت‌ها به‌روزرسانی شد.');
                        } catch (\Throwable $exception) {
                            $record->update(['last_error' => $exception->getMessage()]);
                            ThreeCxNotifier::error($exception, 'دریافت قابلیت‌ها ناموفق بود.');
                        }
                    }),
                Action::make('sync_now')
                    ->label('همگام‌سازی اکنون')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (ThreeCxInstance $record) => $record->xapi_enabled && IamAuthorization::allows('threecx.sync'))
                    ->action(function (ThreeCxInstance $record) {
                        SyncContactsJob::dispatch($record->getKey());
                        SyncCallHistoryJob::dispatch($record->getKey());
                        SyncChatHistoryJob::dispatch($record->getKey());

                        ThreeCxNotifier::success('همگام‌سازی در صف قرار گرفت.');
                    }),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()
                    ->label('حذف')
                    ->modalHeading('حذف اتصال')
                    ->modalSubmitActionLabel('حذف')
                    ->modalCancelActionLabel('انصراف'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThreeCxInstances::route('/'),
            'create' => CreateThreeCxInstance::route('/create'),
            'edit' => EditThreeCxInstance::route('/{record}/edit'),
        ];
    }
}
