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
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\WorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\WorkCalendarResource\Pages\CreateWorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\WorkCalendarResource\Pages\EditWorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\WorkCalendarResource\Pages\ListWorkCalendars;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\WorkCalendarResource\RelationManagers\HolidayRulesRelationManager;

class WorkCalendarResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.calendar';

    protected static ?string $model = WorkCalendar::class;

    protected static ?string $modelLabel = 'تقویم کاری';

    protected static ?string $pluralModelLabel = 'تقویم‌های کاری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

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
                Select::make('calendar_type')
                    ->label('نوع تقویم')
                    ->options([
                        'jalali' => 'جلالی',
                        'gregorian' => 'میلادی',
                    ])
                    ->default('jalali')
                    ->required(),
                TextInput::make('timezone')
                    ->label('منطقه زمانی')
                    ->default('Asia/Tehran')
                    ->required(),
                Toggle::make('is_default')
                    ->label('پیش‌فرض')
                    ->default(false),
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
                TextColumn::make('calendar_type')
                    ->label('نوع تقویم')
                    ->formatStateUsing(fn ($state) => $state === 'gregorian' ? 'میلادی' : 'جلالی'),
                TextColumn::make('timezone')->label('منطقه زمانی'),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            HolidayRulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkCalendars::route('/'),
            'create' => CreateWorkCalendar::route('/create'),
            'edit' => EditWorkCalendar::route('/{record}/edit'),
        ];
    }
}
