<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapMessageResource\Pages\ListMailtrapMessages;
use Haida\FilamentMailtrap\Resources\MailtrapMessageResource\Pages\ViewMailtrapMessage;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Models\MailtrapMessage;
use Haida\MailtrapCore\Services\MailtrapMessageService;

class MailtrapMessageResource extends IamResource
{
    protected static ?string $model = MailtrapMessage::class;

    protected static ?string $permissionPrefix = 'mailtrap.message';

    protected static ?string $modelLabel = 'پیام Mailtrap';

    protected static ?string $pluralModelLabel = 'پیام‌های Mailtrap';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationLabel = 'پیام‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->label('موضوع')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('from_email')->label('فرستنده')->toggleable(),
                TextColumn::make('to_email')->label('گیرنده')->toggleable(),
                TextColumn::make('sent_at')->label('ارسال')->jalaliDateTime()->sortable(),
                IconColumn::make('is_read')->label('خوانده شده')->boolean(),
                TextColumn::make('attachments_count')->label('ضمیمه‌ها')->toggleable(),
            ])
            ->actions([
                Action::make('refresh')
                    ->label('به‌روزرسانی')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (MailtrapMessage $record, MailtrapMessageService $service): void {
                        $connection = MailtrapConnection::query()->find($record->connection_id);
                        $inbox = MailtrapInbox::query()->find($record->inbox_id);
                        if (! $connection || ! $inbox) {
                            Notification::make()->title('اتصال یا Inbox یافت نشد.')->danger()->send();

                            return;
                        }
                        $service->refreshMessageDetails($connection, $inbox, $record);
                        Notification::make()->title('پیام به‌روزرسانی شد.')->success()->send();
                    }),
                ViewAction::make()->label('مشاهده'),
            ])
            ->emptyStateHeading('پیامی یافت نشد')
            ->emptyStateDescription('ابتدا Inbox را همگام‌سازی کنید.')
            ->defaultSort('sent_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات پیام')
                ->schema([
                    TextEntry::make('subject')->label('موضوع'),
                    TextEntry::make('from_email')->label('فرستنده'),
                    TextEntry::make('to_email')->label('گیرنده'),
                    TextEntry::make('sent_at')->label('ارسال')->jalaliDateTime(),
                    TextEntry::make('attachments_count')->label('تعداد ضمیمه‌ها'),
                    TextEntry::make('text_body')->label('متن')->columnSpanFull(),
                    TextEntry::make('html_body')->label('HTML')->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailtrapMessages::route('/'),
            'view' => ViewMailtrapMessage::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 42);
    }
}
