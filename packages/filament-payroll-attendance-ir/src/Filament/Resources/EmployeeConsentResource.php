<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource\Pages\CreateEmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource\Pages\EditEmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource\Pages\ListEmployeeConsents;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class EmployeeConsentResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.consent';

    protected static ?string $model = EmployeeConsent::class;

    protected static ?string $modelLabel = 'رضایت‌نامه';

    protected static ?string $pluralModelLabel = 'رضایت‌نامه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'حریم خصوصی';

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
                Select::make('consent_type')
                    ->label('نوع رضایت')
                    ->options([
                        'location_tracking' => 'موقعیت مکانی',
                        'biometric_verification' => 'بیومتریک',
                    ])
                    ->required(),
                Toggle::make('is_granted')
                    ->label('فعال')
                    ->default(false),
                DateTimePicker::make('granted_at')
                    ->label('زمان ثبت')
                    ->nullable(),
                DateTimePicker::make('revoked_at')
                    ->label('زمان لغو')
                    ->nullable(),
                KeyValue::make('metadata')
                    ->label('متادیتا')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, EmployeeConsent $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('consent_type')
                    ->label('نوع')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'location_tracking' => 'موقعیت مکانی',
                        'biometric_verification' => 'بیومتریک',
                        default => $state,
                    }),
                IconColumn::make('is_granted')->label('فعال')->boolean(),
                TextColumn::make('granted_at')->label('ثبت')->jalaliDateTime(),
                TextColumn::make('revoked_at')->label('لغو')->jalaliDateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeConsents::route('/'),
            'create' => CreateEmployeeConsent::route('/create'),
            'edit' => EditEmployeeConsent::route('/{record}/edit'),
        ];
    }
}
