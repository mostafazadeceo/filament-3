<?php

namespace Haida\FilamentRelograde\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource\Pages\CreateRelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource\Pages\EditRelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeConnectionResource\Pages\ListRelogradeConnections;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Support\RelogradeNotifier;

class RelogradeConnectionResource extends Resource
{
    protected static ?string $model = RelogradeConnection::class;

    protected static ?string $modelLabel = 'اتصال رلوگرید';

    protected static ?string $pluralModelLabel = 'اتصال‌های رلوگرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'اتصال‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 1;

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
                        Select::make('environment')
                            ->label('محیط')
                            ->options([
                                'sandbox' => 'آزمایشی',
                                'production' => 'عملیاتی',
                            ])
                            ->required(),
                        TextInput::make('api_key')
                            ->label('کلید ای‌پی‌آی')
                            ->password()
                            ->revealable()
                            ->helperText('برای حفظ کلید فعلی خالی بگذارید.')
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state): bool => filled($state)),
                        TextInput::make('api_version')
                            ->label('نسخه ای‌پی‌آی')
                            ->default('1.02')
                            ->required(),
                        TextInput::make('base_url')
                            ->label('نشانی پایه')
                            ->default(config('relograde.base_url'))
                            ->required(),
                        Toggle::make('is_default')
                            ->label('اتصال پیش‌فرض')
                            ->helperText('در هر محیط فقط یک اتصال پیش‌فرض مجاز است.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('وب‌هوک')
                    ->schema([
                        TextInput::make('webhook_secret')
                            ->label('رمز وب‌هوک')
                            ->password()
                            ->revealable()
                            ->helperText('هدر محرمانهٔ اختیاری برای اعتبارسنجی وب‌هوک ورودی.')
                            ->dehydrated(fn ($state): bool => filled($state)),
                        TagsInput::make('webhook_allowed_ips')
                            ->label('آی‌پی‌های مجاز')
                            ->placeholder('18.195.134.217')
                            ->helperText('آی‌پی‌های ارسال‌کننده وب‌هوک رلوگرید را اضافه کنید.')
                            ->columnSpanFull(),
                        TextInput::make('webhook_url_display')
                            ->label('نشانی وب‌هوک')
                            ->disabled()
                            ->dehydrated(false)
                            ->afterStateHydrated(fn ($component) => $component->state(url('/relograde/webhook/order-finished')))
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
                TextColumn::make('environment')
                    ->label('محیط')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::environment($state)),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean(),
                TextColumn::make('api_version')->label('نسخه ای‌پی‌آی'),
                TextColumn::make('base_url')->label('نشانی پایه')->toggleable(),
                TextColumn::make('updated_at')->label('آخرین به‌روزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                Action::make('test')
                    ->label('آزمون اتصال')
                    ->icon('heroicon-o-beaker')
                    ->visible(fn () => RelogradeAuthorization::can('settings_manage'))
                    ->action(function (RelogradeConnection $record, RelogradeClientFactory $factory) {
                        try {
                            $client = $factory->make($record);
                            $balances = $client->listAccounts();
                            $summary = collect($balances)
                                ->map(fn ($item) => ($item['currency'] ?? '-').': '.($item['totalAmount'] ?? '-'))
                                ->implode(', ');

                            RelogradeNotifier::success('اتصال موفق بود', $summary ?: 'هیچ موجودی‌ای برنگشت.');
                        } catch (\Throwable $exception) {
                            RelogradeNotifier::error($exception, 'آزمون اتصال ناموفق بود.');
                        }
                    }),
                Action::make('make_default')
                    ->label('تنظیم به پیش‌فرض')
                    ->icon('heroicon-o-star')
                    ->visible(fn (RelogradeConnection $record) => ! $record->is_default)
                    ->action(function (RelogradeConnection $record) {
                        RelogradeConnection::query()
                            ->where('environment', $record->environment)
                            ->update(['is_default' => false]);
                        $record->update(['is_default' => true]);

                        RelogradeNotifier::success('اتصال پیش‌فرض به‌روزرسانی شد.');
                    }),
                EditAction::make()->label('ویرایش'),
                DeleteAction::make()
                    ->label('حذف')
                    ->modalHeading('حذف اتصال')
                    ->modalSubmitActionLabel('حذف')
                    ->modalCancelActionLabel('انصراف'),
            ])
            ->emptyStateHeading('اتصالی ثبت نشده است')
            ->emptyStateDescription('برای شروع، یک اتصال جدید بسازید و کلید ای‌پی‌آی را وارد کنید.')
            ->emptyStateActions([
                \Filament\Actions\CreateAction::make()
                    ->label('ایجاد اتصال')
                    ->visible(fn () => RelogradeAuthorization::can('settings_manage')),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeConnections::route('/'),
            'create' => CreateRelogradeConnection::route('/create'),
            'edit' => EditRelogradeConnection::route('/{record}/edit'),
        ];
    }
}
