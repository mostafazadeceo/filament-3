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
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAdvanceResource\Pages\CreatePayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAdvanceResource\Pages\EditPayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAdvanceResource\Pages\ListPayrollAdvances;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class PayrollAdvanceResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.advance';

    protected static ?string $model = PayrollAdvance::class;

    protected static ?string $modelLabel = 'مساعده';

    protected static ?string $pluralModelLabel = 'مساعده‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

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
                    ->label('مبلغ مساعده')
                    ->numeric()
                    ->required(),
                DatePicker::make('advance_date')
                    ->label('تاریخ')
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'open' => 'باز',
                        'settled' => 'تسویه شده',
                    ])
                    ->default('open')
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
                    ->formatStateUsing(fn ($state, PayrollAdvance $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name)),
                TextColumn::make('amount')->label('مبلغ'),
                TextColumn::make('advance_date')->label('تاریخ')->jalaliDate(),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollAdvances::route('/'),
            'create' => CreatePayrollAdvance::route('/create'),
            'edit' => EditPayrollAdvance::route('/{record}/edit'),
        ];
    }
}
