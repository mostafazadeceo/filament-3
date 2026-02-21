<?php

declare(strict_types=1);

namespace Haida\FilamentChat\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentChat\Filament\Resources\ChatUserLinkResource\Pages\ListChatUserLinks;
use Haida\FilamentChat\Models\ChatUserLink;
use Haida\FilamentChat\Services\ChatConnectionService;

class ChatUserLinkResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = ChatUserLink::class;

    protected static ?string $permissionPrefix = 'chat.user';

    protected static ?string $modelLabel = 'کاربر چت';

    protected static ?string $pluralModelLabel = 'کاربران چت';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'کاربران چت';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات لینک')
                ->schema([
                    static::tenantSelect(),
                    Select::make('chat_connection_id')
                        ->label('اتصال')
                        ->relationship('connection', 'name')
                        ->required(),
                    Select::make('user_id')
                        ->label('کاربر')
                        ->relationship('user', 'name')
                        ->required(),
                    TextInput::make('username')->label('یوزرنیم')->disabled(),
                    TextInput::make('chat_user_id')->label('شناسه در چت')->disabled(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->searchable(),
                TextColumn::make('user.name')->label('کاربر')->searchable(),
                TextColumn::make('user.email')->label('ایمیل')->toggleable(),
                TextColumn::make('username')->label('یوزرنیم')->toggleable(),
                TextColumn::make('chat_user_id')->label('شناسه چت')->toggleable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => __('filament-chat::messages.user_status.'.$state)),
                TextColumn::make('synced_at')->label('آخرین همگام سازی')->jalaliDateTime(),
                TextColumn::make('last_error_message')->label('آخرین خطا')->toggleable(),
            ])
            ->actions([
                Action::make('sync_user')
                    ->label('همگام سازی')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (ChatUserLink $record, ChatConnectionService $service): void {
                        try {
                            $service->syncUser($record->connection, $record->user);
                            Notification::make()
                                ->title('همگام سازی انجام شد')
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
                Action::make('deactivate_user')
                    ->label('غیرفعال سازی')
                    ->icon('heroicon-o-no-symbol')
                    ->requiresConfirmation()
                    ->visible(fn (ChatUserLink $record): bool => $record->status !== 'inactive')
                    ->action(function (ChatUserLink $record, ChatConnectionService $service): void {
                        try {
                            $service->deactivateLink($record);
                            Notification::make()
                                ->title('کاربر در چت غیرفعال شد')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در غیرفعال سازی کاربر')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reactivate_user')
                    ->label('فعال سازی')
                    ->icon('heroicon-o-check-badge')
                    ->requiresConfirmation()
                    ->visible(fn (ChatUserLink $record): bool => $record->status === 'inactive')
                    ->action(function (ChatUserLink $record, ChatConnectionService $service): void {
                        try {
                            $service->reactivateLink($record);
                            Notification::make()
                                ->title('کاربر در چت فعال شد')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در فعال سازی کاربر')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('reset_password')
                    ->label('ریست رمز چت')
                    ->icon('heroicon-o-key')
                    ->form([
                        TextInput::make('password')
                            ->label('رمز عبور جدید')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8)
                            ->maxLength(64),
                    ])
                    ->action(function (ChatUserLink $record, array $data, ChatConnectionService $service): void {
                        try {
                            $service->resetLinkPassword($record, (string) ($data['password'] ?? ''));
                            Notification::make()
                                ->title('رمز عبور کاربر در چت بروزرسانی شد')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در ریست رمز عبور')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                DeleteAction::make()->label('حذف'),
            ])
            ->emptyStateHeading('لینکی ثبت نشده است')
            ->emptyStateDescription('بعد از همگام سازی کاربران، لینک ها ایجاد می شود.')
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatUserLinks::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-chat.navigation.group', 'یکپارچه سازی ها');
    }

    public static function getNavigationSort(): ?int
    {
        return (int) config('filament-chat.navigation.sort', 45) + 1;
    }
}
