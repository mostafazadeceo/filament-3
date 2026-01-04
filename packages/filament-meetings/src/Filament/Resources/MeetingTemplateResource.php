<?php

namespace Haida\FilamentMeetings\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource\Pages\CreateMeetingTemplate;
use Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource\Pages\EditMeetingTemplate;
use Haida\FilamentMeetings\Filament\Resources\MeetingTemplateResource\Pages\ListMeetingTemplates;
use Haida\FilamentMeetings\Models\MeetingTemplate;
use Illuminate\Database\Eloquent\Builder;

class MeetingTemplateResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MeetingTemplate::class;

    protected static ?string $navigationLabel = 'قالب‌های جلسه';

    protected static ?string $pluralModelLabel = 'قالب‌های جلسه';

    protected static ?string $modelLabel = 'قالب جلسه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'جلسات';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')
                    ->label('نام قالب')
                    ->required()
                    ->maxLength(255),
                Select::make('scope')
                    ->label('دامنه')
                    ->options([
                        'workspace' => 'فضای کاری',
                        'personal' => 'شخصی',
                    ])
                    ->default('workspace')
                    ->required(),
                Select::make('format')
                    ->label('فرمت')
                    ->options([
                        'sales' => 'فروش',
                        'standup' => 'استندآپ',
                        'team' => 'تیمی',
                        'custom' => 'سفارشی',
                    ])
                    ->default('team')
                    ->required(),
                CheckboxList::make('sections_enabled_json')
                    ->label('بخش‌های فعال')
                    ->options([
                        'overview' => 'نمای کلی',
                        'action_items' => 'اقدام‌ها',
                        'insights' => 'بینش‌ها',
                        'keywords' => 'کلیدواژه‌ها',
                        'outline' => 'سرفصل‌ها',
                    ])
                    ->columns(2)
                    ->nullable(),
                Textarea::make('custom_prompts_json')
                    ->label('پرامپت‌های سفارشی')
                    ->helperText('ساختار JSON برای پرامپت‌ها ثبت می‌شود.')
                    ->afterStateHydrated(function (Textarea $component, $state): void {
                        if (is_array($state)) {
                            $component->state(json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => $state ? json_decode((string) $state, true) : null)
                    ->rows(4)
                    ->nullable(),
                Textarea::make('minutes_schema_json')
                    ->label('اسکیما صورتجلسه')
                    ->helperText('در صورت نیاز، اسکیما به صورت JSON تعریف شود.')
                    ->afterStateHydrated(function (Textarea $component, $state): void {
                        if (is_array($state)) {
                            $component->state(json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => $state ? json_decode((string) $state, true) : null)
                    ->rows(4)
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('scope')
                    ->label('دامنه')
                    ->formatStateUsing(fn (string $state) => $state === 'personal' ? 'شخصی' : 'فضای کاری'),
                TextColumn::make('format')
                    ->label('فرمت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'sales' => 'فروش',
                        'standup' => 'استندآپ',
                        'team' => 'تیمی',
                        'custom' => 'سفارشی',
                        default => $state,
                    }),
                TextColumn::make('owner.name')->label('مالک'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ])
            ->filters([
                SelectFilter::make('scope')
                    ->label('دامنه')
                    ->options([
                        'workspace' => 'فضای کاری',
                        'personal' => 'شخصی',
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('owner');
        $userId = auth()->id();

        if (! $userId) {
            return $query->where('scope', 'workspace');
        }

        return $query->where(function (Builder $builder) use ($userId) {
            $builder->where('scope', 'workspace')
                ->orWhere(function (Builder $inner) use ($userId) {
                    $inner->where('scope', 'personal')
                        ->where('owner_id', $userId);
                });
        });
    }

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.templates.manage',
        ]);
    }

    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return IamAuthorization::allowsAny([
            'meetings.view',
            'meetings.templates.manage',
        ], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('meetings.templates.manage');
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return IamAuthorization::allows('meetings.templates.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return IamAuthorization::allows('meetings.templates.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMeetingTemplates::route('/'),
            'create' => CreateMeetingTemplate::route('/create'),
            'edit' => EditMeetingTemplate::route('/{record}/edit'),
        ];
    }
}
