<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\FiscalPeriodResource\Pages\CreateFiscalPeriod;
use Vendor\FilamentAccountingIr\Filament\Resources\FiscalPeriodResource\Pages\EditFiscalPeriod;
use Vendor\FilamentAccountingIr\Filament\Resources\FiscalPeriodResource\Pages\ListFiscalPeriods;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\FiscalYear;

class FiscalPeriodResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = FiscalPeriod::class;

    protected static ?string $modelLabel = 'دوره مالی';

    protected static ?string $pluralModelLabel = 'دوره‌های مالی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'دوره‌های مالی';

    protected static string|\UnitEnum|null $navigationGroup = 'هسته حسابداری';

    protected static ?int $navigationSort = 4;

    protected static array $eagerLoad = ['fiscalYear'];

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
                Select::make('fiscal_year_id')
                    ->label('سال مالی')
                    ->options(fn () => FiscalYear::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label('شروع')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('پایان')
                    ->required(),
                Select::make('period_type')
                    ->label('نوع')
                    ->options([
                        'month' => 'ماهانه',
                        'quarter' => 'فصلی',
                    ])
                    ->default('month'),
                Toggle::make('is_closed')
                    ->label('بسته شده')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('fiscalYear.name')->label('سال مالی')->sortable(),
                TextColumn::make('start_date')->label('شروع')->jalaliDate()->sortable(),
                TextColumn::make('end_date')->label('پایان')->jalaliDate()->sortable(),
                ToggleColumn::make('is_closed')->label('بسته'),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFiscalPeriods::route('/'),
            'create' => CreateFiscalPeriod::route('/create'),
            'edit' => EditFiscalPeriod::route('/{record}/edit'),
        ];
    }
}
