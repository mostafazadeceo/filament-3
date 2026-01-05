<?php

namespace Haida\FilamentNotify\Core\Resources;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Core\Models\NotificationRule;
use Haida\FilamentNotify\Core\Models\Template;
use Haida\FilamentNotify\Core\Models\Trigger;
use Haida\FilamentNotify\Core\Resources\NotificationRuleResource\Pages\CreateNotificationRule;
use Haida\FilamentNotify\Core\Resources\NotificationRuleResource\Pages\EditNotificationRule;
use Haida\FilamentNotify\Core\Resources\NotificationRuleResource\Pages\ListNotificationRules;
use Illuminate\Database\Eloquent\Builder;

class NotificationRuleResource extends Resource
{
    protected static ?string $model = NotificationRule::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static ?string $navigationLabel = 'قوانین اطلاع‌رسانی';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاع‌رسانی';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        $channels = collect(app(ChannelRegistry::class)->installed())
            ->mapWithKeys(fn ($driver) => [$driver->key() => $driver->label()])
            ->toArray();

        return $schema
            ->components([
                Section::make('اطلاعات اصلی')
                    ->schema([
                        TextInput::make('name')
                            ->label('عنوان قانون')
                            ->required()
                            ->maxLength(200),
                        Toggle::make('enabled')
                            ->label('فعال')
                            ->default(true),
                        Select::make('trigger_id')
                            ->label('تریگر')
                            ->options(fn () => Trigger::query()->forPanel()->pluck('label', 'id')->toArray())
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('شرایط')
                    ->schema([
                        Select::make('conditions.match')
                            ->label('نوع تطبیق')
                            ->options([
                                'all' => 'همه شروط برقرار باشد',
                                'any' => 'حداقل یکی برقرار باشد',
                            ])
                            ->default('all'),
                        Repeater::make('conditions.rules')
                            ->label('شروط')
                            ->schema([
                                TextInput::make('field')
                                    ->label('فیلد')
                                    ->placeholder('record.status')
                                    ->required(),
                                Select::make('operator')
                                    ->label('عملگر')
                                    ->options([
                                        'equals' => 'برابر',
                                        'not_equals' => 'نابرابر',
                                        'contains' => 'شامل',
                                        'in' => 'در مجموعه',
                                        'not_in' => 'خارج از مجموعه',
                                        'gt' => 'بزرگ‌تر',
                                        'gte' => 'بزرگ‌تر یا برابر',
                                        'lt' => 'کوچک‌تر',
                                        'lte' => 'کوچک‌تر یا برابر',
                                        'exists' => 'وجود دارد',
                                        'empty' => 'خالی است',
                                    ])
                                    ->required(),
                                TextInput::make('value')
                                    ->label('مقدار'),
                            ])
                            ->columns(3)
                            ->default([]),
                    ])
                    ->columns(1),
                Section::make('گیرندگان')
                    ->schema([
                        Repeater::make('recipients')
                            ->label('تعریف گیرندگان')
                            ->schema([
                                Select::make('type')
                                    ->label('نوع')
                                    ->options([
                                        'initiator' => 'کاربر اجراکننده',
                                        'emails' => 'ایمیل‌های مشخص',
                                        'phones' => 'شماره‌های مشخص',
                                        'relation' => 'رابطه از رکورد',
                                        'role' => 'نقش کاربری',
                                    ])
                                    ->required()
                                    ->live(),
                                Textarea::make('emails')
                                    ->label('ایمیل‌ها')
                                    ->placeholder("example@site.com\nexample2@site.com")
                                    ->visible(fn (Get $get): bool => $get('type') === 'emails')
                                    ->rows(3),
                                Textarea::make('phones')
                                    ->label('شماره‌ها')
                                    ->placeholder("+98912...\n+98913...")
                                    ->visible(fn (Get $get): bool => $get('type') === 'phones')
                                    ->rows(3),
                                TextInput::make('path')
                                    ->label('مسیر رابطه')
                                    ->placeholder('record.user')
                                    ->visible(fn (Get $get): bool => $get('type') === 'relation'),
                                TextInput::make('role')
                                    ->label('نام نقش')
                                    ->visible(fn (Get $get): bool => $get('type') === 'role'),
                            ])
                            ->columns(2)
                            ->default([]),
                    ]),
                Section::make('کانال‌ها')
                    ->schema([
                        Repeater::make('channels')
                            ->label('کانال‌های ارسال')
                            ->schema([
                                Toggle::make('enabled')
                                    ->label('فعال')
                                    ->default(true),
                                Select::make('channel')
                                    ->label('کانال')
                                    ->options($channels)
                                    ->required()
                                    ->live(),
                                Select::make('template_id')
                                    ->label('قالب')
                                    ->options(fn (Get $get) => Template::query()
                                        ->forPanel()
                                        ->where('channel', $get('channel'))
                                        ->pluck('name', 'id')
                                        ->toArray())
                                    ->searchable()
                                    ->required(fn (Get $get): bool => filled($get('channel'))),
                            ])
                            ->columns(3)
                            ->default([]),
                    ]),
                Section::make('محدودسازی')
                    ->schema([
                        TextInput::make('throttle.limit')
                            ->label('حداکثر ارسال')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('throttle.seconds')
                            ->label('بازه زمانی (ثانیه)')
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('عنوان')->searchable(),
                TextColumn::make('trigger.label')->label('تریگر'),
                IconColumn::make('enabled')->label('فعال')->boolean(),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotificationRules::route('/'),
            'create' => CreateNotificationRule::route('/create'),
            'edit' => EditNotificationRule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forPanel();
    }
}
