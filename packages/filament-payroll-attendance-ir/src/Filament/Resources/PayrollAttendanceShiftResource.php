<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceShiftResource\Pages\CreatePayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceShiftResource\Pages\EditPayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceShiftResource\Pages\ListPayrollAttendanceShifts;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;

class PayrollAttendanceShiftResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.shift';

    protected static ?string $model = PayrollAttendanceShift::class;

    protected static ?string $modelLabel = 'شیفت';

    protected static ?string $pluralModelLabel = 'شیفت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

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
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                TimePicker::make('start_time')
                    ->label('شروع')
                    ->required(),
                TimePicker::make('end_time')
                    ->label('پایان')
                    ->required(),
                TextInput::make('break_minutes')
                    ->label('دقیقه استراحت')
                    ->numeric()
                    ->default(0),
                Select::make('is_night')
                    ->label('شیفت شب')
                    ->options([
                        true => 'بله',
                        false => 'خیر',
                    ])
                    ->default(false),
                Select::make('is_rotating')
                    ->label('نوبت‌کاری')
                    ->options([
                        true => 'بله',
                        false => 'خیر',
                    ])
                    ->default(false),
                ColorPicker::make('color')
                    ->label('رنگ'),
                Select::make('is_active')
                    ->label('وضعیت')
                    ->options([
                        true => 'فعال',
                        false => 'غیرفعال',
                    ])
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('code')->label('کد'),
                TextColumn::make('start_time')->label('شروع'),
                TextColumn::make('end_time')->label('پایان'),
                TextColumn::make('is_night')->label('شب')->badge(),
                TextColumn::make('is_active')->label('وضعیت')->badge(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollAttendanceShifts::route('/'),
            'create' => CreatePayrollAttendanceShift::route('/create'),
            'edit' => EditPayrollAttendanceShift::route('/{record}/edit'),
        ];
    }
}
