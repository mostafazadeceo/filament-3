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
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollMinimumWageTableResource\Pages\CreatePayrollMinimumWageTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollMinimumWageTableResource\Pages\EditPayrollMinimumWageTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollMinimumWageTableResource\Pages\ListPayrollMinimumWageTables;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMinimumWageTable;

class PayrollMinimumWageTableResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.settings';

    protected static ?string $model = PayrollMinimumWageTable::class;

    protected static ?string $modelLabel = 'حداقل دستمزد';

    protected static ?string $pluralModelLabel = 'جداول حداقل دستمزد';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

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
                TextInput::make('daily_wage')
                    ->label('حداقل مزد روزانه')
                    ->numeric()
                    ->required(),
                TextInput::make('monthly_wage')
                    ->label('حداقل مزد ماهانه')
                    ->numeric()
                    ->required(),
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
                TextColumn::make('daily_wage')->label('روزانه'),
                TextColumn::make('monthly_wage')->label('ماهانه'),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollMinimumWageTables::route('/'),
            'create' => CreatePayrollMinimumWageTable::route('/create'),
            'edit' => EditPayrollMinimumWageTable::route('/{record}/edit'),
        ];
    }
}
