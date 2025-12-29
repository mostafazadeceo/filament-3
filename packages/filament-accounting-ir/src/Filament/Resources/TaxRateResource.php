<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxRateResource\Pages\CreateTaxRate;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxRateResource\Pages\EditTaxRate;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxRateResource\Pages\ListTaxRates;
use Vendor\FilamentAccountingIr\Filament\Resources\TaxRateResource\RelationManagers\TaxRateVersionsRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\TaxRate;

class TaxRateResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = TaxRate::class;

    protected static ?string $modelLabel = 'نرخ مالیات';

    protected static ?string $pluralModelLabel = 'نرخ‌های مالیات';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'نرخ‌های مالیات';

    protected static string|\UnitEnum|null $navigationGroup = 'مالیات و انطباق';

    protected static ?int $navigationSort = 1;

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
                Select::make('tax_type')
                    ->label('نوع')
                    ->options([
                        'vat' => 'ارزش افزوده',
                        'withholding' => 'تکلیفی',
                        'payroll' => 'حقوق',
                    ])
                    ->default('vat'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable(),
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('tax_type')->label('نوع')->badge(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('code');
    }

    public static function getRelations(): array
    {
        return [
            TaxRateVersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTaxRates::route('/'),
            'create' => CreateTaxRate::route('/create'),
            'edit' => EditTaxRate::route('/{record}/edit'),
        ];
    }
}
