<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyTierResource\Pages\CreateLoyaltyTier;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyTierResource\Pages\EditLoyaltyTier;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyTierResource\Pages\ListLoyaltyTiers;
use Haida\FilamentLoyaltyClub\Models\LoyaltyTier;

class LoyaltyTierResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.tier';

    protected static ?string $model = LoyaltyTier::class;

    protected static ?string $navigationLabel = 'سطوح وفاداری';

    protected static ?string $pluralModelLabel = 'سطوح وفاداری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                TextInput::make('slug')->label('کلید')->maxLength(255),
                TextInput::make('rank')->label('ترتیب')->numeric()->default(1),
                TextInput::make('threshold_points')->label('حداقل امتیاز')->numeric()->default(0),
                TextInput::make('threshold_spend')->label('حداقل مبلغ')->numeric()->default(0),
                Textarea::make('benefits')->label('مزایا (JSON)')->rows(3)->columnSpanFull(),
                Toggle::make('is_default')->label('سطح پیش‌فرض')->inline(false),
                Toggle::make('is_active')->label('فعال')->inline(false)->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('rank')->label('ترتیب'),
                TextColumn::make('threshold_points')->label('امتیاز'),
                TextColumn::make('threshold_spend')->label('مبلغ'),
                TextColumn::make('is_active')->label('فعال')->formatStateUsing(fn (bool $state) => $state ? 'بله' : 'خیر'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyTiers::route('/'),
            'create' => CreateLoyaltyTier::route('/create'),
            'edit' => EditLoyaltyTier::route('/{record}/edit'),
        ];
    }
}
