<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource\Pages\CreatePayrollTimePunch;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource\Pages\EditPayrollTimePunch;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource\Pages\ListPayrollTimePunches;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;

class PayrollTimePunchResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.punch';

    protected static ?string $model = PayrollTimePunch::class;

    protected static ?string $modelLabel = 'ثبت تردد';

    protected static ?string $pluralModelLabel = 'ثبت ترددها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

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
                DateTimePicker::make('punch_at')
                    ->label('زمان')
                    ->required(),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'in' => 'ورود',
                        'out' => 'خروج',
                    ])
                    ->required(),
                Select::make('source')
                    ->label('منبع')
                    ->options([
                        'device' => 'دستگاه',
                        'web' => 'پنل',
                        'bot' => 'بات',
                        'manual' => 'دستی',
                    ])
                    ->default('manual')
                    ->required(),
                TextInput::make('device_ref')
                    ->label('شناسه دستگاه')
                    ->maxLength(255),
                TextInput::make('latitude')
                    ->label('عرض جغرافیایی')
                    ->numeric(),
                TextInput::make('longitude')
                    ->label('طول جغرافیایی')
                    ->numeric(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')->label('پرسنل')->formatStateUsing(function ($state, PayrollTimePunch $record) {
                    return trim($record->employee?->first_name.' '.$record->employee?->last_name);
                })->searchable(),
                TextColumn::make('type')->label('نوع')->badge(),
                TextColumn::make('source')->label('منبع')->badge(),
                TextColumn::make('punch_at')->label('زمان')->jalaliDateTime()->sortable(),
            ])
            ->defaultSort('punch_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollTimePunches::route('/'),
            'create' => CreatePayrollTimePunch::route('/create'),
            'edit' => EditPayrollTimePunch::route('/{record}/edit'),
        ];
    }
}
