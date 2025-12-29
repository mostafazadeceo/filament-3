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
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource\Pages\CreatePayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource\Pages\EditPayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource\Pages\ListPayrollEmployees;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class PayrollEmployeeResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.employee';

    protected static ?string $model = PayrollEmployee::class;

    protected static ?string $modelLabel = 'پرسنل';

    protected static ?string $pluralModelLabel = 'پرسنل';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'منابع انسانی';

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
                TextInput::make('employee_no')
                    ->label('شماره پرسنلی')
                    ->maxLength(64),
                TextInput::make('first_name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('نام خانوادگی')
                    ->required()
                    ->maxLength(255),
                TextInput::make('national_id')
                    ->label('کد ملی')
                    ->maxLength(32),
                DatePicker::make('birth_date')
                    ->label('تاریخ تولد')
                    ->nullable(),
                DatePicker::make('employment_date')
                    ->label('تاریخ استخدام')
                    ->nullable(),
                TextInput::make('job_title')
                    ->label('عنوان شغلی')
                    ->maxLength(255),
                Select::make('marital_status')
                    ->label('وضعیت تأهل')
                    ->options([
                        'single' => 'مجرد',
                        'married' => 'متأهل',
                    ])
                    ->default('single')
                    ->required(),
                TextInput::make('children_count')
                    ->label('تعداد فرزند')
                    ->numeric()
                    ->default(0),
                TextInput::make('phone')
                    ->label('موبایل')
                    ->maxLength(32),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        'terminated' => 'خاتمه همکاری',
                    ])
                    ->default('active')
                    ->required(),
                TextInput::make('bank_name')
                    ->label('نام بانک')
                    ->maxLength(255),
                TextInput::make('bank_account')
                    ->label('شماره حساب')
                    ->maxLength(64),
                TextInput::make('bank_sheba')
                    ->label('شبا')
                    ->maxLength(32),
                Textarea::make('metadata')
                    ->label('اطلاعات تکمیلی')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee_no')->label('شماره پرسنلی')->searchable(),
                TextColumn::make('first_name')->label('نام')->searchable(),
                TextColumn::make('last_name')->label('نام خانوادگی')->searchable(),
                TextColumn::make('job_title')->label('عنوان شغلی'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('employment_date')->label('تاریخ استخدام')->jalaliDate(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollEmployees::route('/'),
            'create' => CreatePayrollEmployee::route('/create'),
            'edit' => EditPayrollEmployee::route('/{record}/edit'),
        ];
    }
}
