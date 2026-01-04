<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimesheetResource\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\GenerateTimesheets;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimesheetResource;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate')
                ->label('تولید کاربرگ‌ها')
                ->visible(fn () => auth()->user()?->can('create', Timesheet::class))
                ->form([
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
                    DatePicker::make('period_start')
                        ->label('شروع بازه')
                        ->default(now()->startOfMonth())
                        ->required(),
                    DatePicker::make('period_end')
                        ->label('پایان بازه')
                        ->default(now()->endOfMonth())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $companyId = (int) $data['company_id'];
                    $branchId = $data['branch_id'] ? (int) $data['branch_id'] : null;

                    app(GenerateTimesheets::class)->execute(
                        $companyId,
                        $branchId,
                        Carbon::parse($data['period_start']),
                        Carbon::parse($data['period_end'])
                    );

                    Notification::make()->title('کاربرگ‌ها تولید شدند.')->success()->send();
                }),
        ];
    }
}
