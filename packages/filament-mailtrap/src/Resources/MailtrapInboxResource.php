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
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapInboxResource\Pages\ListMailtrapInboxes;
use Haida\FilamentMailtrap\Resources\MailtrapInboxResource\Pages\ViewMailtrapInbox;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Services\MailtrapInboxService;
use Haida\MailtrapCore\Services\MailtrapMessageService;
use Haida\MailtrapCore\Support\MailtrapLabels;

class MailtrapInboxResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailtrapInbox::class;

    protected static ?string $permissionPrefix = 'mailtrap.inbox';

    protected static ?string $modelLabel = 'Inbox Mailtrap';

    protected static ?string $pluralModelLabel = 'Inboxهای Mailtrap';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Inboxها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات Inbox')
                ->schema([
                    static::tenantSelect(),
                    Select::make('connection_id')
                        ->label('اتصال')
                        ->options(fn () => static::scopeByTenant(MailtrapConnection::query())
                            ->pluck('name', 'id')
                            ->all())
                        ->required(),
                    TextInput::make('project_id')
                        ->label('شناسه پروژه (اختیاری)')
                        ->numeric()
                        ->helperText('برای ساخت Inbox جدید. در صورت خالی بودن از اولین پروژه موجود استفاده می‌شود.'),
                    TextInput::make('name')
                        ->label('نام Inbox')
                        ->required()
                        ->maxLength(150),
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
                TextColumn::make('inbox_id')->label('Inbox ID')->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => MailtrapLabels::inboxStatus($state)),
                TextColumn::make('email_domain')->label('دامنه ایمیل')->toggleable(),
                TextColumn::make('messages_count')->label('تعداد پیام')->sortable(),
                TextColumn::make('unread_count')->label('خوانده‌نشده')->sortable(),
                TextColumn::make('last_message_sent_at')->label('آخرین ارسال')->jalaliDateTime(),
                TextColumn::make('synced_at')->label('آخرین همگام‌سازی')->jalaliDateTime(),
            ])
            ->actions([
                Action::make('sync_messages')
                    ->label('همگام‌سازی پیام‌ها')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (MailtrapInbox $record, MailtrapMessageService $service): void {
                        $connection = MailtrapConnection::query()->find($record->connection_id);
                        if (! $connection) {
                            Notification::make()
                                ->title('اتصال یافت نشد')
                                ->danger()
                                ->send();
                            return;
                        }
                        try {
                            $count = $service->syncMessages($connection, $record, []);
                            Notification::make()
                                ->title('پیام‌ها همگام شدند')
                                ->body('تعداد: ' . count($count))
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در همگام‌سازی پیام‌ها')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                ViewAction::make()->label('مشاهده'),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()
                    ->label('حذف')
                    ->action(function (MailtrapInbox $record, MailtrapInboxService $service): void {
                        $connection = MailtrapConnection::query()->find($record->connection_id);
                        if (! $connection) {
                            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
                            return;
                        }

                        try {
                            $service->delete($connection, $record);
                            Notification::make()->title('Inbox حذف شد.')->success()->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در حذف Inbox')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('Inboxی یافت نشد')
            ->emptyStateDescription('ابتدا اتصال را همگام‌سازی کنید.')
            ->emptyStateActions([
                CreateAction::make()->label('ایجاد Inbox'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailtrapInboxes::route('/'),
            'create' => \Haida\FilamentMailtrap\Resources\MailtrapInboxResource\Pages\CreateMailtrapInbox::route('/create'),
            'edit' => \Haida\FilamentMailtrap\Resources\MailtrapInboxResource\Pages\EditMailtrapInbox::route('/{record}/edit'),
            'view' => ViewMailtrapInbox::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('مشخصات Inbox')
                ->schema([
                TextEntry::make('name')->label('نام'),
                TextEntry::make('inbox_id')->label('Inbox ID'),
                TextEntry::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn ($state) => MailtrapLabels::inboxStatus($state)),
                    TextEntry::make('username')->label('نام کاربری'),
                    TextEntry::make('email_domain')->label('دامنه ایمیل'),
                    TextEntry::make('api_domain')->label('API Domain'),
                    TextEntry::make('messages_count')->label('تعداد پیام'),
                    TextEntry::make('unread_count')->label('خوانده‌نشده'),
                    TextEntry::make('last_message_sent_at')->label('آخرین ارسال')->jalaliDateTime(),
                ])
                ->columns(2),
        ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 41);
    }
}
