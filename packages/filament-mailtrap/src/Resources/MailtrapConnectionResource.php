<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapConnectionResource\Pages\CreateMailtrapConnection;
use Haida\FilamentMailtrap\Resources\MailtrapConnectionResource\Pages\EditMailtrapConnection;
use Haida\FilamentMailtrap\Resources\MailtrapConnectionResource\Pages\ListMailtrapConnections;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapConnectionService;
use Haida\MailtrapCore\Services\MailtrapDomainService;
use Haida\MailtrapCore\Services\MailtrapInboxService;
use Haida\MailtrapCore\Support\MailtrapLabels;

class MailtrapConnectionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailtrapConnection::class;

    protected static ?string $permissionPrefix = 'mailtrap.connection';

    protected static ?string $modelLabel = 'اتصال Mailtrap';

    protected static ?string $pluralModelLabel = 'اتصال‌های Mailtrap';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-at-symbol';

    protected static ?string $navigationLabel = 'اتصال‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 1;

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
                    TextInput::make('api_token')
                        ->label('توکن API (مدیریت)')
                        ->password()
                        ->revealable()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->helperText('برای حفظ توکن فعلی خالی بگذارید.'),
                    TextInput::make('send_api_token')
                        ->label('توکن API ارسال (اختیاری)')
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state): bool => filled($state))
                        ->helperText('در صورت استفاده از Send API جداگانه. برای حفظ مقدار فعلی خالی بگذارید.'),
                    TextInput::make('account_id')
                        ->label('شناسه اکانت')
                        ->numeric()
                        ->helperText('در صورت خالی بودن از API خوانده می‌شود.'),
                    TextInput::make('default_inbox_id')
                        ->label('Inbox پیش‌فرض')
                        ->numeric()
                        ->helperText('اختیاری.'),
                    Select::make('status')
                        ->label('وضعیت')
                        ->options([
                            'active' => 'فعال',
                            'inactive' => 'غیرفعال',
                        ])
                        ->default('active'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => MailtrapLabels::connectionStatus($state)),
                TextColumn::make('account_id')->label('اکانت')->toggleable(),
                TextColumn::make('default_inbox_id')->label('Inbox پیش‌فرض')->toggleable(),
                TextColumn::make('last_tested_at')->label('آخرین آزمون')->jalaliDateTime(),
                TextColumn::make('last_sync_at')->label('آخرین همگام‌سازی')->jalaliDateTime(),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                Action::make('test_connection')
                    ->label('آزمون اتصال')
                    ->icon('heroicon-o-beaker')
                    ->action(function (MailtrapConnection $record, MailtrapConnectionService $service): void {
                        try {
                            $service->testConnection($record);
                            Notification::make()
                                ->title('اتصال بررسی شد')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در ارتباط با Mailtrap')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('sync_inboxes')
                    ->label('همگام‌سازی Inbox')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (MailtrapConnection $record, MailtrapInboxService $service): void {
                        try {
                            $count = $service->sync($record, true);
                            Notification::make()
                                ->title('Inboxها همگام شدند')
                                ->body('تعداد: '.$count)
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در همگام‌سازی Inbox')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('sync_domains')
                    ->label('همگام‌سازی دامنه‌ها')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->action(function (MailtrapConnection $record, MailtrapDomainService $service): void {
                        try {
                            $count = $service->sync($record, true);
                            Notification::make()
                                ->title('دامنه‌ها همگام شدند')
                                ->body('تعداد: '.$count)
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در همگام‌سازی دامنه‌ها')
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
            'index' => ListMailtrapConnections::route('/'),
            'create' => CreateMailtrapConnection::route('/create'),
            'edit' => EditMailtrapConnection::route('/{record}/edit'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 40);
    }
}
