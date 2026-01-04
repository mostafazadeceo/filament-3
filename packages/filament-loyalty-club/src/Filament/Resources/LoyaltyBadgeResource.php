<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyBadgeResource\Pages\CreateLoyaltyBadge;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyBadgeResource\Pages\EditLoyaltyBadge;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyBadgeResource\Pages\ListLoyaltyBadges;
use Haida\FilamentLoyaltyClub\Models\LoyaltyBadge;

class LoyaltyBadgeResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.badge';

    protected static ?string $model = LoyaltyBadge::class;

    protected static ?string $navigationLabel = 'نشان‌ها';

    protected static ?string $pluralModelLabel = 'نشان‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                TextInput::make('icon')->label('آیکن')->maxLength(255),
                Textarea::make('description')->label('توضیحات')->rows(3),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['active' => 'فعال', 'inactive' => 'غیرفعال'])
                    ->default('active'),
                Textarea::make('perks')->label('مزایا (JSON)')->rows(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyBadges::route('/'),
            'create' => CreateLoyaltyBadge::route('/create'),
            'edit' => EditLoyaltyBadge::route('/{record}/edit'),
        ];
    }
}
