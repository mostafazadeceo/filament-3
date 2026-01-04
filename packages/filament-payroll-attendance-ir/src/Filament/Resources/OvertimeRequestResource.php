<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\RequestStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\OvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource\Pages\CreateOvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource\Pages\EditOvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource\Pages\ListOvertimeRequests;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class OvertimeRequestResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.overtime';

    protected static ?string $model = OvertimeRequest::class;

    protected static ?string $modelLabel = 'درخواست اضافه‌کار';

    protected static ?string $pluralModelLabel = 'درخواست‌های اضافه‌کار';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

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
                DatePicker::make('work_date')
                    ->label('تاریخ انجام')
                    ->required(),
                TextInput::make('requested_minutes')
                    ->label('دقایق درخواست')
                    ->numeric()
                    ->required(),
                Textarea::make('reason')
                    ->label('دلیل')
                    ->rows(3)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        RequestStatus::Pending->value => 'در انتظار',
                        RequestStatus::Approved->value => 'تایید شده',
                        RequestStatus::Rejected->value => 'رد شده',
                        RequestStatus::Cancelled->value => 'لغو شده',
                    ])
                    ->default(RequestStatus::Pending->value)
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, OvertimeRequest $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('work_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('requested_minutes')->label('دقایق'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => self::statusLabel($state)),
                TextColumn::make('approved_at')->label('تایید')->jalaliDateTime(),
            ])
            ->actions([
                TableAction::make('approve')
                    ->label('تایید')
                    ->visible(fn (OvertimeRequest $record) => ($record->status?->value ?? (string) $record->status) === RequestStatus::Pending->value
                        && auth()->user()?->can('approve', $record))
                    ->requiresConfirmation()
                    ->action(function (OvertimeRequest $record): void {
                        $record->update([
                            'status' => RequestStatus::Approved->value,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),
                TableAction::make('reject')
                    ->label('رد')
                    ->visible(fn (OvertimeRequest $record) => ($record->status?->value ?? (string) $record->status) === RequestStatus::Pending->value
                        && auth()->user()?->can('approve', $record))
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('علت رد')
                            ->required(),
                    ])
                    ->action(function (OvertimeRequest $record, array $data): void {
                        $record->update([
                            'status' => RequestStatus::Rejected->value,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'metadata' => array_merge((array) ($record->metadata ?? []), [
                                'rejection_reason' => $data['rejection_reason'] ?? null,
                            ]),
                        ]);
                    }),
            ])
            ->defaultSort('work_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOvertimeRequests::route('/'),
            'create' => CreateOvertimeRequest::route('/create'),
            'edit' => EditOvertimeRequest::route('/{record}/edit'),
        ];
    }

    private static function statusLabel(string $status): string
    {
        return match ($status) {
            RequestStatus::Approved->value => 'تایید شده',
            RequestStatus::Rejected->value => 'رد شده',
            RequestStatus::Cancelled->value => 'لغو شده',
            default => 'در انتظار',
        };
    }
}
