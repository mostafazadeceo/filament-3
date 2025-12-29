<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\DimensionResource\Pages\CreateDimension;
use Vendor\FilamentAccountingIr\Filament\Resources\DimensionResource\Pages\EditDimension;
use Vendor\FilamentAccountingIr\Filament\Resources\DimensionResource\Pages\ListDimensions;
use Vendor\FilamentAccountingIr\Filament\Resources\DimensionResource\RelationManagers\DimensionValuesRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\Dimension;

class DimensionResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = Dimension::class;

    protected static ?string $modelLabel = 'بُعد';

    protected static ?string $pluralModelLabel = 'ابعاد حسابداری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $navigationLabel = 'ابعاد حسابداری';

    protected static string|\UnitEnum|null $navigationGroup = 'هسته حسابداری';

    protected static ?int $navigationSort = 6;

    protected static array $eagerLoad = ['company'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(64),
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
                TextColumn::make('code')->label('کد')->searchable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            DimensionValuesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDimensions::route('/'),
            'create' => CreateDimension::route('/create'),
            'edit' => EditDimension::route('/{record}/edit'),
        ];
    }
}
