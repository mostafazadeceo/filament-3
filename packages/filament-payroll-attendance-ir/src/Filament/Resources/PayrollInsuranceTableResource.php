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
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollInsuranceTableResource\Pages\CreatePayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollInsuranceTableResource\Pages\EditPayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollInsuranceTableResource\Pages\ListPayrollInsuranceTables;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollInsuranceTable;

class PayrollInsuranceTableResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.settings';

    protected static ?string $model = PayrollInsuranceTable::class;

    protected static ?string $modelLabel = 'جدول بیمه';

    protected static ?string $pluralModelLabel = 'جداول بیمه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

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
                TextInput::make('employee_rate')
                    ->label('نرخ سهم کارمند (%)')
                    ->numeric()
                    ->default(7),
                TextInput::make('employer_rate')
                    ->label('نرخ سهم کارفرما (%)')
                    ->numeric()
                    ->default(23),
                TextInput::make('max_insurable_daily')
                    ->label('سقف روزانه')
                    ->numeric(),
                TextInput::make('max_insurable_monthly')
                    ->label('سقف ماهانه')
                    ->numeric(),
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
                TextColumn::make('employee_rate')->label('سهم کارمند'),
                TextColumn::make('employer_rate')->label('سهم کارفرما'),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollInsuranceTables::route('/'),
            'create' => CreatePayrollInsuranceTable::route('/create'),
            'edit' => EditPayrollInsuranceTable::route('/{record}/edit'),
        ];
    }
}
