<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyMissionResource\Pages\CreateLoyaltyMission;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyMissionResource\Pages\EditLoyaltyMission;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyMissionResource\Pages\ListLoyaltyMissions;
use Haida\FilamentLoyaltyClub\Models\LoyaltyBadge;
use Haida\FilamentLoyaltyClub\Models\LoyaltyMission;

class LoyaltyMissionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.mission';

    protected static ?string $model = LoyaltyMission::class;

    protected static ?string $navigationLabel = 'ماموریت‌ها';

    protected static ?string $pluralModelLabel = 'ماموریت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                Textarea::make('description')->label('توضیحات')->rows(3),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'count' => 'تکرار',
                        'amount' => 'مبلغی',
                        'category' => 'دسته‌بندی',
                        'referral' => 'دعوت',
                    ])
                    ->default('count'),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['active' => 'فعال', 'inactive' => 'غیرفعال'])
                    ->default('active'),
                Textarea::make('criteria')->label('معیارها (JSON)')->rows(3)->columnSpanFull(),
                TextInput::make('reward_points')->label('امتیاز پاداش')->numeric()->default(0),
                TextInput::make('reward_cashback')->label('کش‌بک پاداش')->numeric()->default(0),
                Select::make('badge_id')
                    ->label('نشان')
                    ->options(fn () => LoyaltyBadge::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                DateTimePicker::make('start_at')->label('شروع')->nullable(),
                DateTimePicker::make('end_at')->label('پایان')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('reward_points')->label('امتیاز'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyMissions::route('/'),
            'create' => CreateLoyaltyMission::route('/create'),
            'edit' => EditLoyaltyMission::route('/{record}/edit'),
        ];
    }
}
