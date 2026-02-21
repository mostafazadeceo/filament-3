<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentChat\Filament\Resources\ChatConnectionResource\Pages\CreateChatConnection;
use Haida\FilamentChat\Filament\Resources\ChatConnectionResource\Pages\EditChatConnection;
use Haida\FilamentChat\Filament\Resources\ChatConnectionResource\Pages\ListChatConnections;
use Haida\FilamentChat\Models\ChatConnection;
use Haida\FilamentChat\Services\ChatConnectionService;

class ChatConnectionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = ChatConnection::class;

    protected static ?string $permissionPrefix = 'chat.connection';

    protected static ?string $modelLabel = 'اتصال چت';

    protected static ?string $pluralModelLabel = 'اتصال های چت';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'اتصال های چت';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('تنظیمات اتصال')
                ->schema([
                    static::tenantSelect(),
                    TextInput::make('name')
                        ->label('نام اتصال')
                        ->required()
                        ->maxLength(150)
                        ->scopedUnique(ignoreRecord: true)
                        ->validationMessages([
                            'unique' => 'این نام اتصال قبلاً برای همین فضای کاری ثبت شده است.',
                        ]),
                    Select::make('provider')
                        ->label('ارائه دهنده')
                        ->options([
                            'rocket_chat' => __('filament-chat::messages.provider.rocket_chat'),
                        ])
                        ->default('rocket_chat')
                        ->required(),
                    TextInput::make('base_url')
                        ->label('آدرس پایه')
                        ->required()
                        ->url()
                        ->placeholder('https://chat.example.com'),
                    TextInput::make('settings.team_prefix')
                        ->label('پیشوند تیم')
                        ->placeholder('tenant-')
                        ->helperText('برای نام تیم‌های Rocket.Chat (مثلاً tenant-{slug}).'),
                    TextInput::make('settings.room_prefix')
                        ->label('پیشوند اتاق')
                        ->placeholder('room-')
                        ->helperText('برای نام اتاق‌های پیش‌فرض هر سازمان.'),
                    TextInput::make('api_user_id')
                        ->label('شناسه کاربر ادمین')
                        ->required(),
                    TextInput::make('api_token')
                        ->label('توکن API (ادمین)')
                        ->password()
                        ->revealable()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->helperText('برای حفظ توکن فعلی خالی بگذارید.'),
                    TagsInput::make('settings.default_roles')
                        ->label('نقش های پیش فرض')
                        ->helperText('برای Rocket.Chat: پیش فرض user.'),
                    KeyValue::make('settings.role_map')
                        ->label('نقشه نقش‌ها (IAM → Rocket.Chat)')
                        ->keyLabel('نقش IAM')
                        ->valueLabel('نقش Rocket.Chat')
                        ->helperText('مثال: tenant_owner => owner'),
                    Select::make('status')
                        ->label('وضعیت')
                        ->options([
                            'active' => __('filament-chat::messages.connection_status.active'),
                            'inactive' => __('filament-chat::messages.connection_status.inactive'),
                            'error' => __('filament-chat::messages.connection_status.error'),
                        ])
                        ->default('active'),
                ])
                ->columns(2),
            Section::make('OIDC (راهنما)')
                ->schema([
                    TextInput::make('oidc_discovery_url')
                        ->label('Discovery URL')
                        ->default(function (): string {
                            $issuer = (string) config('filamat-iam.sso.oidc.issuer', config('app.url'));
                            $issuer = rtrim($issuer, '/');
                            return $issuer.'/.well-known/openid-configuration';
                        })
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('oidc_issuer')
                        ->label('Issuer')
                        ->default(fn () => (string) config('filamat-iam.sso.oidc.issuer', ''))
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->helperText('آدرس Issuer برای OIDC.'),
                    TextInput::make('oidc_client_id')
                        ->label('Client ID')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->helperText('در صورت خالی بودن خودکار ساخته می شود.'),
                    TextInput::make('oidc_client_secret')
                        ->label('Client Secret')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->helperText('برای حفظ مقدار فعلی خالی بگذارید.'),
                    TextInput::make('oidc_scopes')
                        ->label('Scopes')
                        ->default(function (): string {
                            $scopes = config('filamat-iam.sso.oidc.allowed_scopes', 'openid profile email');
                            if (is_array($scopes)) {
                                return implode(' ', $scopes);
                            }
                            return (string) $scopes;
                        })
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->placeholder('openid profile email'),
                    TagsInput::make('settings.oidc_redirect_uris')
                        ->label('Redirect URIs')
                        ->helperText('اگر خالی باشد، از مسیرهای پیش فرض Rocket.Chat استفاده می شود.'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('provider')
                    ->label('ارائه دهنده')
                    ->formatStateUsing(fn ($state) => $state === 'rocket_chat'
                        ? __('filament-chat::messages.provider.rocket_chat')
                        : $state),
                TextColumn::make('base_url')->label('آدرس')->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament-chat::messages.connection_status.'.$state)),
                TextColumn::make('last_tested_at')->label('آخرین آزمون')->jalaliDateTime(),
                TextColumn::make('last_sync_at')->label('آخرین همگام سازی')->jalaliDateTime(),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                Action::make('test_connection')
                    ->label('آزمون اتصال')
                    ->icon('heroicon-o-beaker')
                    ->action(function (ChatConnection $record, ChatConnectionService $service): void {
                        try {
                            $service->testConnection($record);
                            Notification::make()
                                ->title('اتصال بررسی شد')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در ارتباط با چت')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('rotate_oidc')
                    ->label('چرخش OIDC')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->action(function (ChatConnection $record, ChatConnectionService $service): void {
                        $service->rotateOidcCredentials($record);
                        Notification::make()
                            ->title('کلیدهای OIDC بروزرسانی شد')
                            ->success()
                            ->send();
                    }),
                Action::make('sync_users')
                    ->label('همگام سازی کاربران')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (ChatConnection $record, ChatConnectionService $service): void {
                        try {
                            $count = $service->syncUsers($record);
                            Notification::make()
                                ->title('همگام سازی انجام شد')
                                ->body('تعداد: '.$count)
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در همگام سازی')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()->label('حذف'),
            ])
            ->emptyStateHeading('اتصالی ثبت نشده است')
            ->emptyStateDescription('برای شروع، یک اتصال جدید بسازید و توکن API را وارد کنید.')
            ->emptyStateActions([
                CreateAction::make()->label('ایجاد اتصال'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatConnections::route('/'),
            'create' => CreateChatConnection::route('/create'),
            'edit' => EditChatConnection::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-chat.navigation.group', 'یکپارچه سازی ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-chat.navigation.sort', 45);
    }
}
