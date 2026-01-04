<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\PolicyStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\AttendancePolicyResource\Pages\CreateAttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\AttendancePolicyResource\Pages\EditAttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\AttendancePolicyResource\Pages\ListAttendancePolicies;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;

class AttendancePolicyResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.policy';

    protected static ?string $model = AttendancePolicy::class;

    protected static ?string $modelLabel = 'سیاست حضور و غیاب';

    protected static ?string $pluralModelLabel = 'سیاست‌های حضور و غیاب';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

    protected static array $eagerLoad = ['company', 'branch'];

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
                TextInput::make('name')
                    ->label('عنوان')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        PolicyStatus::Active->value => 'فعال',
                        PolicyStatus::Inactive->value => 'غیرفعال',
                    ])
                    ->default(PolicyStatus::Active->value)
                    ->required(),
                Toggle::make('is_default')
                    ->label('پیش‌فرض')
                    ->default(false),
                Toggle::make('requires_consent')
                    ->label('نیازمند رضایت')
                    ->default(true),
                Toggle::make('allow_remote_work')
                    ->label('اجازه دورکاری')
                    ->default(false),
                KeyValue::make('rules')
                    ->label('قواعد سیاست')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->helperText('کلیدهای متداول: require_geofence, require_wifi, require_device_ref, late_grace_minutes, shift_end_grace_minutes, break_deduction_minutes, max_overtime_minutes, max_travel_speed_kmh, min_event_interval_minutes, remote_only_if_branch, exception_assignee_id')
                    ->columnSpanFull(),
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
                TextColumn::make('name')->label('عنوان')->searchable(),
                TextColumn::make('company.name')->label('شرکت'),
                TextColumn::make('branch.name')->label('شعبه'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === PolicyStatus::Active->value ? 'فعال' : 'غیرفعال'),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean(),
                IconColumn::make('requires_consent')->label('رضایت')->boolean(),
                IconColumn::make('allow_remote_work')->label('دورکاری')->boolean(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendancePolicies::route('/'),
            'create' => CreateAttendancePolicy::route('/create'),
            'edit' => EditAttendancePolicy::route('/{record}/edit'),
        ];
    }
}
