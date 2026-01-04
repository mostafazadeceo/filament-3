<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimesheetStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimesheetResource\Pages\ListTimesheets;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimesheetResource\Pages\ViewTimesheet;

class TimesheetResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.timesheet';

    protected static ?string $model = Timesheet::class;

    protected static ?string $modelLabel = 'کاربرگ';

    protected static ?string $pluralModelLabel = 'کاربرگ‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

    protected static array $eagerLoad = ['employee'];

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, Timesheet $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('period_start')->label('شروع')->jalaliDate()->sortable(),
                TextColumn::make('period_end')->label('پایان')->jalaliDate()->sortable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => self::statusLabel($state)),
                TextColumn::make('worked_minutes')->label('کارکرد'),
                TextColumn::make('overtime_minutes')->label('اضافه‌کار'),
                TextColumn::make('late_minutes')->label('تاخیر'),
                TextColumn::make('absence_minutes')->label('غیبت'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        TimesheetStatus::Draft->value => 'پیش‌نویس',
                        TimesheetStatus::Approved->value => 'تایید شده',
                        TimesheetStatus::Locked->value => 'قفل شده',
                    ]),
            ])
            ->actions([
                TableAction::make('approve')
                    ->label('تایید')
                    ->visible(fn (Timesheet $record) => ($record->status?->value ?? (string) $record->status) === TimesheetStatus::Draft->value
                        && auth()->user()?->can('approve', $record))
                    ->requiresConfirmation()
                    ->action(function (Timesheet $record): void {
                        $record->update([
                            'status' => TimesheetStatus::Approved->value,
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    }),
            ])
            ->defaultSort('period_start', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTimesheets::route('/'),
            'view' => ViewTimesheet::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('جزئیات کاربرگ')
                    ->schema([
                        TextEntry::make('employee_id')
                            ->label('پرسنل')
                            ->formatStateUsing(fn ($state, Timesheet $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name)),
                        TextEntry::make('period_start')->label('شروع')->jalaliDate(),
                        TextEntry::make('period_end')->label('پایان')->jalaliDate(),
                        TextEntry::make('status')
                            ->label('وضعیت')
                            ->formatStateUsing(fn ($state) => self::statusLabel($state)),
                        TextEntry::make('worked_minutes')->label('کارکرد'),
                        TextEntry::make('overtime_minutes')->label('اضافه‌کار'),
                        TextEntry::make('night_minutes')->label('شب‌کاری'),
                        TextEntry::make('friday_minutes')->label('جمعه‌کاری'),
                        TextEntry::make('holiday_minutes')->label('تعطیل'),
                        TextEntry::make('late_minutes')->label('تاخیر'),
                        TextEntry::make('early_leave_minutes')->label('تعجیل خروج'),
                        TextEntry::make('absence_minutes')->label('غیبت'),
                        TextEntry::make('approved_at')->label('تایید')->jalaliDateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    private static function statusLabel(string $status): string
    {
        return match ($status) {
            TimesheetStatus::Approved->value => 'تایید شده',
            TimesheetStatus::Locked->value => 'قفل شده',
            default => 'پیش‌نویس',
        };
    }
}
