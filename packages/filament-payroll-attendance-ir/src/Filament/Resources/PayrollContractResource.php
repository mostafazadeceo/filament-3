<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollContractResource\Pages\CreatePayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollContractResource\Pages\EditPayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollContractResource\Pages\ListPayrollContracts;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class PayrollContractResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.contract';

    protected static ?string $model = PayrollContract::class;

    protected static ?string $modelLabel = 'قرارداد';

    protected static ?string $pluralModelLabel = 'قراردادها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'منابع انسانی';

    protected static array $eagerLoad = ['employee'];

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
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('employee_id')
                    ->label('پرسنل')
                    ->options(fn () => PayrollEmployee::query()->selectRaw("id, CONCAT(first_name, ' ', last_name) as name")
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->required(),
                Select::make('scope')
                    ->label('نوع قرارداد')
                    ->options([
                        'official' => 'رسمی',
                        'internal' => 'داخلی',
                    ])
                    ->default('official')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                DatePicker::make('effective_from')
                    ->label('شروع')
                    ->required(),
                DatePicker::make('effective_to')
                    ->label('پایان')
                    ->nullable(),
                TextInput::make('base_salary')
                    ->label('حقوق پایه')
                    ->numeric()
                    ->default(0),
                TextInput::make('daily_hours')
                    ->label('ساعات روزانه')
                    ->numeric()
                    ->default(8),
                TextInput::make('weekly_hours')
                    ->label('ساعات هفتگی')
                    ->numeric()
                    ->default(44),
                TextInput::make('monthly_hours')
                    ->label('ساعات ماهانه')
                    ->numeric()
                    ->default(176),
                TextInput::make('housing_allowance')
                    ->label('حق مسکن')
                    ->numeric()
                    ->default(0),
                TextInput::make('food_allowance')
                    ->label('بن')
                    ->numeric()
                    ->default(0),
                TextInput::make('child_allowance')
                    ->label('حق اولاد')
                    ->numeric()
                    ->default(0),
                TextInput::make('marriage_allowance')
                    ->label('حق تأهل')
                    ->numeric()
                    ->default(0),
                TextInput::make('seniority_allowance')
                    ->label('پایه سنوات')
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label('یادداشت')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')->label('نام')->formatStateUsing(function ($state, PayrollContract $record) {
                    return trim($record->employee?->first_name.' '.$record->employee?->last_name);
                })->searchable(),
                TextColumn::make('scope')->label('نوع')->badge(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('base_salary')->label('حقوق پایه'),
                TextColumn::make('effective_from')->label('شروع')->jalaliDate(),
                TextColumn::make('effective_to')->label('پایان')->jalaliDate(),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollContracts::route('/'),
            'create' => CreatePayrollContract::route('/create'),
            'edit' => EditPayrollContract::route('/{record}/edit'),
        ];
    }
}
