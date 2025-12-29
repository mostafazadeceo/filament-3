<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceScheduleResource\Pages\CreatePayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceScheduleResource\Pages\EditPayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceScheduleResource\Pages\ListPayrollAttendanceSchedules;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class PayrollAttendanceScheduleResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.schedule';

    protected static ?string $model = PayrollAttendanceSchedule::class;

    protected static ?string $modelLabel = 'برنامه شیفت';

    protected static ?string $pluralModelLabel = 'برنامه شیفت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

    protected static array $eagerLoad = ['employee', 'shift'];

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
                Select::make('shift_id')
                    ->label('شیفت')
                    ->options(fn () => PayrollAttendanceShift::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                DatePicker::make('work_date')
                    ->label('تاریخ')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'scheduled' => 'برنامه‌ریزی شده',
                        'off' => 'تعطیل',
                        'leave' => 'مرخصی',
                    ])
                    ->default('scheduled')
                    ->required(),
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
                TextColumn::make('employee.first_name')->label('پرسنل')->formatStateUsing(function ($state, PayrollAttendanceSchedule $record) {
                    return trim($record->employee?->first_name.' '.$record->employee?->last_name);
                })->searchable(),
                TextColumn::make('shift.name')->label('شیفت'),
                TextColumn::make('work_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('work_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollAttendanceSchedules::route('/'),
            'create' => CreatePayrollAttendanceSchedule::route('/create'),
            'edit' => EditPayrollAttendanceSchedule::route('/{record}/edit'),
        ];
    }
}
