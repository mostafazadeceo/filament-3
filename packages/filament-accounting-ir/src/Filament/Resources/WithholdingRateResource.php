<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\WithholdingRateResource\Pages\CreateWithholdingRate;
use Vendor\FilamentAccountingIr\Filament\Resources\WithholdingRateResource\Pages\EditWithholdingRate;
use Vendor\FilamentAccountingIr\Filament\Resources\WithholdingRateResource\Pages\ListWithholdingRates;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\WithholdingRate;

class WithholdingRateResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = WithholdingRate::class;

    protected static ?string $modelLabel = 'نرخ تکلیفی';

    protected static ?string $pluralModelLabel = 'نرخ‌های تکلیفی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $navigationLabel = 'نرخ‌های تکلیفی';

    protected static string|\UnitEnum|null $navigationGroup = 'مالیات و انطباق';

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
                TextInput::make('rate')
                    ->label('نرخ')
                    ->numeric()
                    ->minValue(0),
                DatePicker::make('effective_from')
                    ->label('از تاریخ'),
                DatePicker::make('effective_to')
                    ->label('تا تاریخ'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable(),
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('rate')->label('نرخ')->numeric(decimalPlaces: 4),
                TextColumn::make('effective_from')->label('از')->jalaliDate(),
                TextColumn::make('effective_to')->label('تا')->jalaliDate(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWithholdingRates::route('/'),
            'create' => CreateWithholdingRate::route('/create'),
            'edit' => EditWithholdingRate::route('/{record}/edit'),
        ];
    }
}
