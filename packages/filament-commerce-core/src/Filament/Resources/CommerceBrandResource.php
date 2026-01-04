<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceBrandResource\Pages\CreateCommerceBrand;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceBrandResource\Pages\EditCommerceBrand;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceBrandResource\Pages\ListCommerceBrands;
use Haida\FilamentCommerceCore\Models\CommerceBrand;

class CommerceBrandResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.catalog';

    protected static ?string $model = CommerceBrand::class;

    protected static ?string $modelLabel = 'برند';

    protected static ?string $pluralModelLabel = 'برندها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('اسلاگ')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('slug')->label('اسلاگ'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceBrands::route('/'),
            'create' => CreateCommerceBrand::route('/create'),
            'edit' => EditCommerceBrand::route('/{record}/edit'),
        ];
    }
}
