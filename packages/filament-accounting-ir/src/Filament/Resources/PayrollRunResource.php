<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\Pages\CreatePayrollRun;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\Pages\EditPayrollRun;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\Pages\ListPayrollRuns;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\RelationManagers\PayrollItemsRelationManager;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollRunResource\RelationManagers\PayrollSlipsRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\PayrollRun;

class PayrollRunResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = PayrollRun::class;

    protected static ?string $modelLabel = 'دوره حقوق';

    protected static ?string $pluralModelLabel = 'دوره‌های حقوق';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'دوره‌های حقوق';

    protected static string|\UnitEnum|null $navigationGroup = 'حقوق و دستمزد';

    protected static ?int $navigationSort = 2;

    protected static array $eagerLoad = ['company'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('fiscal_period_id')
                    ->label('دوره مالی')
                    ->options(fn () => FiscalPeriod::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                DatePicker::make('run_date')
                    ->label('تاریخ اجرا'),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'approved' => 'تایید',
                        'posted' => 'قطعی',
                    ])
                    ->default('draft'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('run_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('run_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PayrollItemsRelationManager::class,
            PayrollSlipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollRuns::route('/'),
            'create' => CreatePayrollRun::route('/create'),
            'edit' => EditPayrollRun::route('/{record}/edit'),
        ];
    }
}
