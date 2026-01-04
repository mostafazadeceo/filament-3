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
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyPointsRuleResource\Pages\CreateLoyaltyPointsRule;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyPointsRuleResource\Pages\EditLoyaltyPointsRule;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyPointsRuleResource\Pages\ListLoyaltyPointsRules;
use Haida\FilamentLoyaltyClub\Models\LoyaltyPointsRule;

class LoyaltyPointsRuleResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.rule';

    protected static ?string $model = LoyaltyPointsRule::class;

    protected static ?string $navigationLabel = 'قوانین امتیاز';

    protected static ?string $pluralModelLabel = 'قوانین امتیاز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                TextInput::make('event_type')->label('نوع رویداد')->required()->maxLength(64),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['active' => 'فعال', 'inactive' => 'غیرفعال'])
                    ->default('active')
                    ->required(),
                TextInput::make('priority')->label('اولویت')->numeric()->default(100),
                Select::make('scope_type')
                    ->label('دامنه')
                    ->options(['global' => 'سراسری', 'branch' => 'شعبه', 'channel' => 'کانال'])
                    ->default('global'),
                TextInput::make('scope_ref')->label('شناسه دامنه')->maxLength(255),
                Select::make('points_type')
                    ->label('نوع امتیاز')
                    ->options(['fixed' => 'ثابت', 'percent' => 'درصدی'])
                    ->default('fixed'),
                TextInput::make('points_value')->label('امتیاز ثابت')->numeric()->default(0),
                TextInput::make('percent_rate')->label('نرخ درصدی')->numeric(),
                TextInput::make('min_amount')->label('حداقل مبلغ')->numeric(),
                TextInput::make('max_points')->label('سقف امتیاز')->numeric(),
                Select::make('cap_period')
                    ->label('دوره سقف')
                    ->options(['daily' => 'روزانه', 'weekly' => 'هفتگی', 'monthly' => 'ماهانه'])
                    ->nullable(),
                TextInput::make('cap_count')->label('سقف امتیاز در دوره')->numeric(),
                DateTimePicker::make('valid_from')->label('شروع اعتبار')->nullable(),
                DateTimePicker::make('valid_until')->label('پایان اعتبار')->nullable(),
                Textarea::make('conditions')->label('شرایط (JSON)')->rows(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('event_type')->label('رویداد'),
                TextColumn::make('points_type')->label('نوع'),
                TextColumn::make('points_value')->label('امتیاز'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyPointsRules::route('/'),
            'create' => CreateLoyaltyPointsRule::route('/create'),
            'edit' => EditLoyaltyPointsRule::route('/{record}/edit'),
        ];
    }
}
