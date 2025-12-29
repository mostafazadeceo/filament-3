<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveTypeResource\Pages\CreatePayrollLeaveType;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveTypeResource\Pages\EditPayrollLeaveType;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveTypeResource\Pages\ListPayrollLeaveTypes;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveType;

class PayrollLeaveTypeResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.leave';

    protected static ?string $model = PayrollLeaveType::class;

    protected static ?string $modelLabel = 'نوع مرخصی';

    protected static ?string $pluralModelLabel = 'انواع مرخصی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

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
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'paid' => 'با حقوق',
                        'unpaid' => 'بدون حقوق',
                        'sick' => 'استعلاجی',
                    ])
                    ->default('paid')
                    ->required(),
                TextInput::make('default_days_per_year')
                    ->label('روز سالانه')
                    ->numeric()
                    ->default(0),
                Select::make('requires_approval')
                    ->label('نیاز به تایید')
                    ->options([
                        true => 'بله',
                        false => 'خیر',
                    ])
                    ->default(true),
                Select::make('requires_document')
                    ->label('مدرک الزامی')
                    ->options([
                        true => 'بله',
                        false => 'خیر',
                    ])
                    ->default(false),
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
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('type')->label('نوع')->badge(),
                TextColumn::make('default_days_per_year')->label('روز سالانه'),
                TextColumn::make('is_active')->label('وضعیت')->badge(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollLeaveTypes::route('/'),
            'create' => CreatePayrollLeaveType::route('/create'),
            'edit' => EditPayrollLeaveType::route('/{record}/edit'),
        ];
    }
}
