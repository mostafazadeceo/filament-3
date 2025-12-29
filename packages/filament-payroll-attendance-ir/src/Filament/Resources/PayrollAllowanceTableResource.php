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
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAllowanceTableResource\Pages\CreatePayrollAllowanceTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAllowanceTableResource\Pages\EditPayrollAllowanceTable;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAllowanceTableResource\Pages\ListPayrollAllowanceTables;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAllowanceTable;

class PayrollAllowanceTableResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.settings';

    protected static ?string $model = PayrollAllowanceTable::class;

    protected static ?string $modelLabel = 'مزایای قانونی';

    protected static ?string $pluralModelLabel = 'جداول مزایا';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

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
                TextInput::make('housing_allowance')
                    ->label('حق مسکن')
                    ->numeric()
                    ->default(0),
                TextInput::make('food_allowance')
                    ->label('بن')
                    ->numeric()
                    ->default(0),
                TextInput::make('child_allowance_daily')
                    ->label('حق اولاد روزانه')
                    ->numeric()
                    ->default(0),
                TextInput::make('marriage_allowance')
                    ->label('حق تأهل')
                    ->numeric()
                    ->default(0),
                TextInput::make('seniority_allowance_daily')
                    ->label('سنوات روزانه')
                    ->numeric()
                    ->default(0),
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
                TextColumn::make('housing_allowance')->label('مسکن'),
                TextColumn::make('food_allowance')->label('بن'),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollAllowanceTables::route('/'),
            'create' => CreatePayrollAllowanceTable::route('/create'),
            'edit' => EditPayrollAllowanceTable::route('/{record}/edit'),
        ];
    }
}
