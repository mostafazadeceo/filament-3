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
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLoanResource\Pages\CreatePayrollLoan;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLoanResource\Pages\EditPayrollLoan;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLoanResource\Pages\ListPayrollLoans;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLoanResource\RelationManagers\PayrollLoanInstallmentsRelationManager;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoan;

class PayrollLoanResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.loan';

    protected static ?string $model = PayrollLoan::class;

    protected static ?string $modelLabel = 'وام';

    protected static ?string $pluralModelLabel = 'وام‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'حقوق و دستمزد';

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
                TextInput::make('amount')
                    ->label('مبلغ وام')
                    ->numeric()
                    ->required(),
                TextInput::make('installment_count')
                    ->label('تعداد اقساط')
                    ->numeric()
                    ->default(1),
                TextInput::make('installment_amount')
                    ->label('مبلغ قسط')
                    ->numeric()
                    ->default(0),
                DatePicker::make('start_date')
                    ->label('تاریخ شروع')
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'closed' => 'تسویه شده',
                    ])
                    ->default('active')
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_id')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, PayrollLoan $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name)),
                TextColumn::make('amount')->label('مبلغ'),
                TextColumn::make('installment_count')->label('اقساط'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('start_date')->label('شروع')->jalaliDate(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PayrollLoanInstallmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollLoans::route('/'),
            'create' => CreatePayrollLoan::route('/create'),
            'edit' => EditPayrollLoan::route('/{record}/edit'),
        ];
    }
}
