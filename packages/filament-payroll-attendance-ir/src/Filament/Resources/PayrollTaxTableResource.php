<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTaxTableResource\Pages\CreatePayrollTaxTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTaxTableResource\Pages\EditPayrollTaxTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTaxTableResource\Pages\ListPayrollTaxTables;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTaxTableResource\RelationManagers\PayrollTaxBracketsRelationManager;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;

class PayrollTaxTableResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.settings';

    protected static ?string $model = PayrollTaxTable::class;

    protected static ?string $modelLabel = 'جدول مالیات';

    protected static ?string $pluralModelLabel = 'جداول مالیات';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-percent-badge';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات حقوق';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                DatePicker::make('effective_from')
                    ->label('از تاریخ')
                    ->required(),
                DatePicker::make('effective_to')
                    ->label('تا تاریخ')
                    ->nullable(),
                TextInput::make('exemption_amount')
                    ->label('معافیت')
                    ->numeric()
                    ->default(0),
                TextInput::make('flat_allowance_rate')
                    ->label('نرخ ثابت مزایا (%)')
                    ->numeric()
                    ->default(10),
                TextInput::make('description')
                    ->label('توضیح')
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('effective_from')->label('از')->jalaliDate()->sortable(),
                TextColumn::make('effective_to')->label('تا')->jalaliDate(),
                TextColumn::make('exemption_amount')->label('معافیت'),
                TextColumn::make('flat_allowance_rate')->label('نرخ ثابت'),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PayrollTaxBracketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollTaxTables::route('/'),
            'create' => CreatePayrollTaxTable::route('/create'),
            'edit' => EditPayrollTaxTable::route('/{record}/edit'),
        ];
    }
}
