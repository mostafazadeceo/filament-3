<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\Pages\CreateMailtrapCampaign;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\Pages\EditMailtrapCampaign;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\Pages\ListMailtrapCampaigns;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\Pages\ViewMailtrapCampaign;
use Haida\FilamentMailtrap\Resources\MailtrapCampaignResource\RelationManagers\MailtrapCampaignSendsRelationManager;
use Haida\MailtrapCore\Models\MailtrapAudience;
use Haida\MailtrapCore\Models\MailtrapCampaign;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapCampaignService;
use Haida\MailtrapCore\Support\MailtrapLabels;
use Illuminate\Database\Eloquent\Builder;

class MailtrapCampaignResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailtrapCampaign::class;

    protected static ?string $permissionPrefix = 'mailtrap.campaign';

    protected static ?string $modelLabel = 'کمپین ایمیلی';

    protected static ?string $pluralModelLabel = 'کمپین‌های ایمیلی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationLabel = 'کمپین‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 46;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('مشخصات کمپین')
                ->schema([
                    static::tenantSelect(),
                    Select::make('connection_id')
                        ->label('اتصال')
                        ->options(fn () => static::scopeByTenant(MailtrapConnection::query())
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->required(),
                    Select::make('audience_id')
                        ->label('لیست مخاطبان')
                        ->options(fn () => static::scopeByTenant(MailtrapAudience::query())
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable(),
                    TextInput::make('name')
                        ->label('نام کمپین')
                        ->required()
                        ->maxLength(190),
                    TextInput::make('subject')
                        ->label('عنوان ایمیل')
                        ->required()
                        ->maxLength(190),
                    TextInput::make('from_email')
                        ->label('ایمیل فرستنده')
                        ->email()
                        ->maxLength(190),
                    TextInput::make('from_name')
                        ->label('نام فرستنده')
                        ->maxLength(190),
                    Select::make('status')
                        ->label('وضعیت')
                        ->options([
                            'draft' => 'پیش‌نویس',
                            'scheduled' => 'زمان‌بندی شده',
                            'sending' => 'در حال ارسال',
                            'sent' => 'ارسال شد',
                            'failed' => 'ناموفق',
                        ])
                        ->default('draft'),
                    DateTimePicker::make('scheduled_at')
                        ->label('زمان ارسال')
                        ->seconds(false)
                        ->helperText('در صورت انتخاب، کمپین زمان‌بندی می‌شود.'),
                ])
                ->columns(2),
            Section::make('محتوا')
                ->schema([
                    Textarea::make('text_body')
                        ->label('متن ساده')
                        ->rows(6)
                        ->columnSpanFull(),
                    Textarea::make('html_body')
                        ->label('بدنه HTML')
                        ->rows(10)
                        ->columnSpanFull(),
                ]),
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
                    ->formatStateUsing(fn ($state) => MailtrapLabels::campaignStatus($state)),
                TextColumn::make('audience.name')->label('لیست مخاطبان')->toggleable(),
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('scheduled_at')->label('زمان‌بندی')->jalaliDateTime()->toggleable(),
                TextColumn::make('stats')
                    ->label('آمار ارسال')
                    ->formatStateUsing(function ($state): string {
                        $sent = data_get($state, 'sent', 0);
                        $failed = data_get($state, 'failed', 0);
                        $pending = data_get($state, 'pending', 0);

                        return "ارسال‌شده: {$sent} | ناموفق: {$failed} | در صف: {$pending}";
                    })
                    ->toggleable(),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['audience', 'connection']))
            ->actions([
                Action::make('send')
                    ->label('ارسال')
                    ->icon('heroicon-o-paper-airplane')
                    ->requiresConfirmation()
                    ->visible(fn (MailtrapCampaign $record): bool => IamAuthorization::allows('mailtrap.campaign.send', IamAuthorization::resolveTenantFromRecord($record))
                        && in_array($record->status, ['draft', 'scheduled'], true))
                    ->action(function (MailtrapCampaign $record, MailtrapCampaignService $service): void {
                        $result = $service->dispatchWithSchedule($record);

                        if ($result === 'no-audience') {
                            Notification::make()->title('لیست مخاطب انتخاب نشده است.')->danger()->send();

                            return;
                        }

                        Notification::make()
                            ->title($result === 'scheduled' ? 'کمپین زمان‌بندی شد.' : 'ارسال کمپین آغاز شد.')
                            ->success()
                            ->send();
                    }),
                ViewAction::make()->label('مشاهده'),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()->label('حذف'),
            ])
            ->emptyStateHeading('کمپینی ثبت نشده است')
            ->emptyStateDescription('برای شروع، یک کمپین جدید بسازید.')
            ->emptyStateActions([
                CreateAction::make()->label('ایجاد کمپین'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات کمپین')
                ->schema([
                    TextEntry::make('name')->label('نام'),
                    TextEntry::make('status')
                        ->label('وضعیت')
                        ->formatStateUsing(fn ($state) => MailtrapLabels::campaignStatus($state)),
                    TextEntry::make('subject')->label('عنوان ایمیل'),
                    TextEntry::make('from_email')->label('ایمیل فرستنده'),
                    TextEntry::make('from_name')->label('نام فرستنده'),
                    TextEntry::make('scheduled_at')->label('زمان‌بندی')->jalaliDateTime(),
                    TextEntry::make('started_at')->label('شروع ارسال')->jalaliDateTime(),
                    TextEntry::make('finished_at')->label('پایان ارسال')->jalaliDateTime(),
                    TextEntry::make('stats')
                        ->label('آمار')
                        ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : null)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            MailtrapCampaignSendsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailtrapCampaigns::route('/'),
            'create' => CreateMailtrapCampaign::route('/create'),
            'edit' => EditMailtrapCampaign::route('/{record}/edit'),
            'view' => ViewMailtrapCampaign::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 46);
    }
}
