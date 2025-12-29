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
use Vendor\FilamentAccountingIr\Filament\Resources\TaxCategoryResource\Pages\CreateTaxCategory;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxCategoryResource\Pages\EditTaxCategory;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxCategoryResource\Pages\ListTaxCategories;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\TaxCategory;

class TaxCategoryResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = TaxCategory::class;

    protected static ?string $modelLabel = 'رده مالیاتی';

    protected static ?string $pluralModelLabel = 'رده‌های مالیاتی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'رده‌های مالیاتی';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاعات پایه';

    protected static ?int $navigationSort = 3;

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
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(64),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('vat_rate')
                    ->label('نرخ ارزش افزوده')
                    ->numeric()
                    ->minValue(0),
                Toggle::make('is_exempt')
                    ->label('معاف')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable(),
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('vat_rate')->label('نرخ')->numeric(decimalPlaces: 2),
                ToggleColumn::make('is_exempt')->label('معاف'),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxCategories::route('/'),
            'create' => CreateTaxCategory::route('/create'),
            'edit' => EditTaxCategory::route('/{record}/edit'),
        ];
    }
}
