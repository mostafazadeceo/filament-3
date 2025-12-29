<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceRecordResource\Pages\CreatePayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceRecordResource\Pages\EditPayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceRecordResource\Pages\ListPayrollAttendanceRecords;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class PayrollAttendanceRecordResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.attendance';

    protected static ?string $model = PayrollAttendanceRecord::class;

    protected static ?string $modelLabel = 'کارکرد';

    protected static ?string $pluralModelLabel = 'کارکردها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

    protected static array $eagerLoad = ['employee', 'shift'];

    public static function canEdit($record): bool
    {
        return $record instanceof PayrollAttendanceRecord
            && $record->status === 'draft'
            && auth()->user()?->can('update', $record);
    }

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
                DateTimePicker::make('scheduled_in')
                    ->label('شروع برنامه')
                    ->nullable(),
                DateTimePicker::make('scheduled_out')
                    ->label('پایان برنامه')
                    ->nullable(),
                DateTimePicker::make('actual_in')
                    ->label('شروع واقعی')
                    ->nullable(),
                DateTimePicker::make('actual_out')
                    ->label('پایان واقعی')
                    ->nullable(),
                TextInput::make('worked_minutes')
                    ->label('دقایق کارکرد')
                    ->numeric()
                    ->default(0),
                TextInput::make('overtime_minutes')
                    ->label('دقایق اضافه‌کار')
                    ->numeric()
                    ->default(0),
                TextInput::make('night_minutes')
                    ->label('دقایق شب‌کاری')
                    ->numeric()
                    ->default(0),
                TextInput::make('friday_minutes')
                    ->label('دقایق جمعه‌کاری')
                    ->numeric()
                    ->default(0),
                TextInput::make('late_minutes')
                    ->label('دقایق تأخیر')
                    ->numeric()
                    ->default(0),
                TextInput::make('early_leave_minutes')
                    ->label('دقایق تعجیل خروج')
                    ->numeric()
                    ->default(0),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'approved' => 'تایید شده',
                        'locked' => 'قفل شده',
                    ])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, PayrollAttendanceRecord $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('work_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('worked_minutes')->label('کارکرد'),
                TextColumn::make('overtime_minutes')->label('اضافه‌کار'),
                TextColumn::make('late_minutes')->label('تأخیر'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->actions([
                TableAction::make('approve')
                    ->label('تایید')
                    ->visible(fn (PayrollAttendanceRecord $record) => $record->status === 'draft' && auth()->user()?->can('approve', $record))
                    ->requiresConfirmation()
                    ->action(function (PayrollAttendanceRecord $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('کارکرد تایید شد.')->success()->send();
                    }),
            ])
            ->defaultSort('work_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollAttendanceRecords::route('/'),
            'create' => CreatePayrollAttendanceRecord::route('/create'),
            'edit' => EditPayrollAttendanceRecord::route('/{record}/edit'),
        ];
    }
}
