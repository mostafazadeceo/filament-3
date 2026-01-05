<?php

namespace Haida\FilamentMeetings\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentMeetings\Filament\Pages\MeetingRoomPage;
use Haida\FilamentMeetings\Filament\Resources\MeetingResource\Pages\CreateMeeting;
use Haida\FilamentMeetings\Filament\Resources\MeetingResource\Pages\EditMeeting;
use Haida\FilamentMeetings\Filament\Resources\MeetingResource\Pages\ListMeetings;
use Haida\FilamentMeetings\Models\Meeting;
use Illuminate\Database\Eloquent\Builder;

class MeetingResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'meetings';

    protected static ?string $model = Meeting::class;

    protected static ?string $navigationLabel = 'جلسات';

    protected static ?string $pluralModelLabel = 'جلسات';

    protected static ?string $modelLabel = 'جلسه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static string|\UnitEnum|null $navigationGroup = 'جلسات';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'scheduled' => 'برنامه‌ریزی شده',
                        'running' => 'در حال برگزاری',
                        'completed' => 'تکمیل شده',
                        'archived' => 'بایگانی شده',
                    ])
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('scheduled_at')
                    ->label('زمان شروع')
                    ->nullable(),
                TextInput::make('duration_minutes')
                    ->label('مدت (دقیقه)')
                    ->numeric()
                    ->minValue(0)
                    ->nullable(),
                Select::make('location_type')
                    ->label('نوع برگزاری')
                    ->options([
                        'online' => 'آنلاین',
                        'onsite' => 'حضوری',
                    ])
                    ->default('online')
                    ->required(),
                TextInput::make('location_value')
                    ->label('جزئیات مکان/لینک')
                    ->maxLength(255)
                    ->nullable(),
                Select::make('organizer_id')
                    ->label('برگزارکننده')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Toggle::make('ai_enabled')
                    ->label('فعال‌سازی هوش مصنوعی')
                    ->default(false),
                Toggle::make('consent_required')
                    ->label('الزام رضایت')
                    ->default(true),
                Select::make('consent_mode')
                    ->label('نوع رضایت')
                    ->options([
                        'manual' => 'تأیید برگزارکننده',
                        'per_attendee' => 'تأیید هر شرکت‌کننده',
                    ])
                    ->default('manual')
                    ->required(),
                Select::make('share_minutes_mode')
                    ->label('اشتراک صورتجلسه')
                    ->options([
                        'private' => 'خصوصی',
                        'attendees' => 'همه حاضران',
                        'selected_roles' => 'نقش‌های منتخب',
                    ])
                    ->default('private')
                    ->required(),
                Select::make('minutes_format')
                    ->label('قالب صورتجلسه')
                    ->options([
                        'sales' => 'فروش',
                        'standup' => 'استندآپ',
                        'team' => 'تیمی',
                        'custom' => 'سفارشی',
                    ])
                    ->default('team')
                    ->required(),
                Textarea::make('meta')
                    ->label('متادیتا')
                    ->rows(3)
                    ->helperText('برای داده‌های اضافی ساختار JSON وارد کنید.')
                    ->afterStateHydrated(function (Textarea $component, $state): void {
                        if (is_array($state)) {
                            $component->state(json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => $state ? json_decode((string) $state, true) : null)
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('scheduled_at')->label('زمان')->dateTime('Y-m-d H:i'),
                TextColumn::make('organizer.name')->label('برگزارکننده'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft' => 'پیش‌نویس',
                        'scheduled' => 'برنامه‌ریزی شده',
                        'running' => 'در حال برگزاری',
                        'completed' => 'تکمیل شده',
                        'archived' => 'بایگانی شده',
                        default => $state,
                    }),
                IconColumn::make('ai_enabled')->label('هوش مصنوعی')->boolean(),
                TextColumn::make('attendees_count')->label('حاضران')->counts('attendees'),
                TextColumn::make('action_items_count')->label('اقدام‌ها')->counts('actionItems'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'scheduled' => 'برنامه‌ریزی شده',
                        'running' => 'در حال برگزاری',
                        'completed' => 'تکمیل شده',
                        'archived' => 'بایگانی شده',
                    ]),
                SelectFilter::make('organizer_id')
                    ->label('برگزارکننده')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray()),
            ])
            ->actions([
                Action::make('room')
                    ->label('اتاق جلسه')
                    ->icon('heroicon-o-sparkles')
                    ->url(fn (Meeting $record) => MeetingRoomPage::getUrl(['record' => $record->getKey()]))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['organizer'])
            ->withCount(['attendees', 'actionItems']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMeetings::route('/'),
            'create' => CreateMeeting::route('/create'),
            'edit' => EditMeeting::route('/{record}/edit'),
        ];
    }
}
