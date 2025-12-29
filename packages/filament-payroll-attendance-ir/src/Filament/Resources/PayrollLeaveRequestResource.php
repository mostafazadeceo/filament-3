<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource\Pages\CreatePayrollLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource\Pages\EditPayrollLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource\Pages\ListPayrollLeaveRequests;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveType;

class PayrollLeaveRequestResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.leave';

    protected static ?string $model = PayrollLeaveRequest::class;

    protected static ?string $modelLabel = 'درخواست مرخصی';

    protected static ?string $pluralModelLabel = 'درخواست‌های مرخصی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

    protected static array $eagerLoad = ['employee', 'leaveType'];

    public static function canEdit($record): bool
    {
        return $record instanceof PayrollLeaveRequest
            && $record->status === 'pending'
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
                Select::make('leave_type_id')
                    ->label('نوع مرخصی')
                    ->options(fn () => PayrollLeaveType::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                DatePicker::make('start_date')
                    ->label('از تاریخ')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('تا تاریخ')
                    ->required(),
                Textarea::make('notes')
                    ->label('توضیحات')
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                        'cancelled' => 'لغو شده',
                    ])
                    ->default('pending')
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
                    ->formatStateUsing(fn ($state, PayrollLeaveRequest $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('leaveType.name')->label('نوع مرخصی'),
                TextColumn::make('start_date')->label('از')->jalaliDate(),
                TextColumn::make('end_date')->label('تا')->jalaliDate(),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->actions([
                TableAction::make('approve')
                    ->label('تایید')
                    ->visible(fn (PayrollLeaveRequest $record) => $record->status === 'pending' && auth()->user()?->can('approve', $record))
                    ->requiresConfirmation()
                    ->action(function (PayrollLeaveRequest $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('مرخصی تایید شد.')->success()->send();
                    }),
                TableAction::make('reject')
                    ->label('رد')
                    ->visible(fn (PayrollLeaveRequest $record) => $record->status === 'pending' && auth()->user()?->can('approve', $record))
                    ->requiresConfirmation()
                    ->action(function (PayrollLeaveRequest $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('مرخصی رد شد.')->warning()->send();
                    }),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollLeaveRequests::route('/'),
            'create' => CreatePayrollLeaveRequest::route('/create'),
            'edit' => EditPayrollLeaveRequest::route('/{record}/edit'),
        ];
    }
}
