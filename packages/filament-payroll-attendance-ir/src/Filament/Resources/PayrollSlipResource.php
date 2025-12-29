<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\Pages\EditPayrollSlip;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\Pages\ListPayrollSlips;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource\RelationManagers\PayrollItemsRelationManager;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollSlip;

class PayrollSlipResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.slip';

    protected static ?string $model = PayrollSlip::class;

    protected static ?string $modelLabel = 'فیش حقوقی';

    protected static ?string $pluralModelLabel = 'فیش‌های حقوقی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-refund';

    protected static string|\UnitEnum|null $navigationGroup = 'حقوق و دستمزد';

    protected static array $eagerLoad = ['employee', 'run'];

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('payroll_run_id')
                    ->label('دوره حقوق')
                    ->options(fn () => PayrollRun::query()->pluck('id', 'id')->toArray())
                    ->disabled()
                    ->dehydrated(false),
                Select::make('employee_id')
                    ->label('پرسنل')
                    ->options(fn () => PayrollEmployee::query()->selectRaw("id, CONCAT(first_name, ' ', last_name) as name")
                        ->pluck('name', 'id')
                        ->toArray())
                    ->disabled()
                    ->dehydrated(false),
                Select::make('scope')
                    ->label('نوع')
                    ->options([
                        'official' => 'رسمی',
                        'internal' => 'داخلی',
                    ])
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
                    ->formatStateUsing(fn ($state, PayrollSlip $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('run.period_start')->label('شروع')->jalaliDate(),
                TextColumn::make('scope')->label('نوع')->badge(),
                TextColumn::make('gross_amount')->label('ناخالص'),
                TextColumn::make('net_amount')->label('خالص'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PayrollItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollSlips::route('/'),
            'edit' => EditPayrollSlip::route('/{record}/edit'),
        ];
    }
}
