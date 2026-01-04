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
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource\Pages\ListMailtrapSendingDomains;
use Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource\Pages\ViewMailtrapSendingDomain;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapSendingDomain;
use Haida\MailtrapCore\Services\MailtrapDomainService;

class MailtrapSendingDomainResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailtrapSendingDomain::class;

    protected static ?string $permissionPrefix = 'mailtrap.domain';

    protected static ?string $modelLabel = 'دامنه Mailtrap';

    protected static ?string $pluralModelLabel = 'دامنه‌های Mailtrap';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'دامنه‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات دامنه')
                ->schema([
                    static::tenantSelect(),
                    Select::make('connection_id')
                        ->label('اتصال')
                        ->options(fn () => static::scopeByTenant(MailtrapConnection::query())
                            ->pluck('name', 'id')
                            ->all())
                        ->required(),
                    TextInput::make('domain_name')
                        ->label('دامنه')
                        ->required()
                        ->maxLength(190),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('domain_name')->label('دامنه')->searchable(),
                IconColumn::make('dns_verified')
                    ->label('DNS تایید شده')
                    ->boolean(),
                TextColumn::make('compliance_status')->label('وضعیت تطابق')->toggleable(),
                IconColumn::make('demo')->label('Demo')->boolean(),
                TextColumn::make('synced_at')->label('آخرین همگام‌سازی')->jalaliDateTime(),
            ])
            ->actions([
                Action::make('sync')
                    ->label('همگام‌سازی')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (MailtrapSendingDomain $record, MailtrapDomainService $service): void {
                        $connection = MailtrapConnection::query()->find($record->connection_id);
                        if (! $connection) {
                            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
                            return;
                        }
                        try {
                            $service->sync($connection, true);
                            Notification::make()->title('دامنه‌ها همگام شدند.')->success()->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در همگام‌سازی دامنه‌ها')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                ViewAction::make()->label('مشاهده'),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()
                    ->label('حذف')
                    ->action(function (MailtrapSendingDomain $record, MailtrapDomainService $service): void {
                        $connection = MailtrapConnection::query()->find($record->connection_id);
                        if (! $connection) {
                            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
                            return;
                        }

                        try {
                            $service->delete($connection, $record);
                            Notification::make()->title('دامنه حذف شد.')->success()->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('خطا در حذف دامنه')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->emptyStateHeading('دامنه‌ای یافت نشد')
            ->emptyStateDescription('ابتدا اتصال را همگام‌سازی کنید.')
            ->emptyStateActions([
                CreateAction::make()->label('ایجاد دامنه'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات دامنه')
                ->schema([
                    TextEntry::make('domain_name')->label('دامنه'),
                    TextEntry::make('dns_verified')
                        ->label('DNS تایید شده')
                        ->formatStateUsing(fn ($state) => $state ? 'بله' : 'خیر'),
                    TextEntry::make('compliance_status')->label('وضعیت تطابق'),
                    TextEntry::make('demo')
                        ->label('Demo')
                        ->formatStateUsing(fn ($state) => $state ? 'بله' : 'خیر'),
                    TextEntry::make('dns_records')
                        ->label('رکوردهای DNS')
                        ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailtrapSendingDomains::route('/'),
            'create' => \Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource\Pages\CreateMailtrapSendingDomain::route('/create'),
            'edit' => \Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource\Pages\EditMailtrapSendingDomain::route('/{record}/edit'),
            'view' => ViewMailtrapSendingDomain::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 43);
    }
}
