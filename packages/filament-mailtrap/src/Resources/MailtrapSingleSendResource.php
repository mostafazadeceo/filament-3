<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource\Pages\CreateMailtrapSingleSend;
use Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource\Pages\ListMailtrapSingleSends;
use Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource\Pages\ViewMailtrapSingleSend;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapSingleSend;
use Haida\MailtrapCore\Support\MailtrapLabels;

class MailtrapSingleSendResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailtrapSingleSend::class;

    protected static ?string $permissionPrefix = null;

    protected static ?string $modelLabel = 'ارسال تکی';

    protected static ?string $pluralModelLabel = 'ارسال‌های تکی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'ارسال تکی';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 47;

    protected static function permissionMap(): array
    {
        return [
            'viewAny' => 'mailtrap.send.single',
            'view' => 'mailtrap.send.single',
            'create' => 'mailtrap.send.single',
            'update' => 'mailtrap.send.single',
            'delete' => 'mailtrap.send.single',
            'restore' => 'mailtrap.send.single',
            'forceDelete' => 'mailtrap.send.single',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('ارسال ایمیل')
                ->schema([
                    static::tenantSelect(),
                    Select::make('connection_id')
                        ->label('اتصال')
                        ->options(fn () => static::scopeByTenant(MailtrapConnection::query())
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->required(),
                    TextInput::make('sandbox_inbox_id')
                        ->label('Inbox سندباکس (اختیاری)')
                        ->numeric()
                        ->helperText('در صورت تکمیل، ارسال به Inbox انجام می‌شود و به ایمیل واقعی ارسال نخواهد شد.'),
                    TextInput::make('to_email')
                        ->label('ایمیل گیرنده')
                        ->email()
                        ->required()
                        ->maxLength(190),
                    TextInput::make('to_name')
                        ->label('نام گیرنده')
                        ->maxLength(190),
                    TextInput::make('subject')
                        ->label('موضوع')
                        ->required()
                        ->maxLength(190),
                    TextInput::make('from_email')
                        ->label('ایمیل فرستنده (اختیاری)')
                        ->email()
                        ->maxLength(190)
                        ->default(fn () => config('filament-notify-mailtrap.default_from_address'))
                        ->helperText('اگر خالی بگذارید از مقدار پیش‌فرض استفاده می‌شود.'),
                    TextInput::make('from_name')
                        ->label('نام فرستنده (اختیاری)')
                        ->maxLength(190)
                        ->default(fn () => config('filament-notify-mailtrap.default_from_name'))
                        ->helperText('اگر خالی بگذارید از مقدار پیش‌فرض استفاده می‌شود.'),
                    Textarea::make('text_body')
                        ->label('متن ساده')
                        ->rows(5)
                        ->columnSpanFull(),
                    Textarea::make('html_body')
                        ->label('بدنه HTML')
                        ->rows(8)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('to_email')->label('گیرنده')->searchable(),
                TextColumn::make('subject')->label('موضوع')->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => MailtrapLabels::sendStatus($state)),
                TextColumn::make('sent_at')->label('زمان ارسال')->jalaliDateTime()->toggleable(),
                TextColumn::make('error_message')->label('خطا')->limit(40)->toggleable(),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
                DeleteAction::make()->label('حذف'),
            ])
            ->emptyStateHeading('ارسال تکی ثبت نشده است')
            ->emptyStateDescription('برای ارسال یک ایمیل تکی از Mailtrap استفاده کنید.')
            ->emptyStateActions([
                CreateAction::make()->label('ارسال ایمیل تکی'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات ارسال')
                ->schema([
                    TextEntry::make('to_email')->label('گیرنده'),
                    TextEntry::make('to_name')->label('نام گیرنده'),
                    TextEntry::make('subject')->label('موضوع'),
                    TextEntry::make('status')
                        ->label('وضعیت')
                        ->formatStateUsing(fn ($state) => MailtrapLabels::sendStatus($state)),
                    TextEntry::make('sent_at')->label('زمان ارسال')->jalaliDateTime(),
                    TextEntry::make('error_message')->label('خطا'),
                    TextEntry::make('text_body')->label('متن')->columnSpanFull(),
                    TextEntry::make('html_body')->label('HTML')->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailtrapSingleSends::route('/'),
            'create' => CreateMailtrapSingleSend::route('/create'),
            'view' => ViewMailtrapSingleSend::route('/{record}'),
        ];
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-mailtrap.navigation.group', 'یکپارچه‌سازی‌ها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-mailtrap.navigation.sort', 47);
    }
}
