<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\VatPeriodResource\Pages\CreateVatPeriod;
use Vendor\FilamentAccountingIr\Filament\Resources\VatPeriodResource\Pages\EditVatPeriod;
use Vendor\FilamentAccountingIr\Filament\Resources\VatPeriodResource\Pages\ListVatPeriods;
use Vendor\FilamentAccountingIr\Filament\Resources\VatPeriodResource\RelationManagers\VatReportsRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\VatPeriod;
use Vendor\FilamentAccountingIr\Services\VatReportService;

class VatPeriodResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = VatPeriod::class;

    protected static ?string $modelLabel = 'دوره ارزش افزوده';

    protected static ?string $pluralModelLabel = 'دوره‌های ارزش افزوده';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'دوره‌های ارزش افزوده';

    protected static string|\UnitEnum|null $navigationGroup = 'مالیات و انطباق';

    protected static ?int $navigationSort = 1;

    protected static array $eagerLoad = ['company', 'fiscalYear'];

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
                Select::make('fiscal_year_id')
                    ->label('سال مالی')
                    ->options(fn () => FiscalYear::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                DatePicker::make('period_start')
                    ->label('شروع')
                    ->required(),
                DatePicker::make('period_end')
                    ->label('پایان')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'open' => 'باز',
                        'closed' => 'بسته',
                    ])
                    ->default('open'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_start')->label('شروع')->jalaliDate()->sortable(),
                TextColumn::make('period_end')->label('پایان')->jalaliDate()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->actions([
                TableAction::make('generate')
                    ->label('تولید اظهارنامه')
                    ->visible(fn (VatPeriod $record) => $record->status === 'open')
                    ->action(function (VatPeriod $record): void {
                        app(VatReportService::class)->generate($record);
                        Notification::make()->title('اظهارنامه تولید شد.')->success()->send();
                    }),
            ])
            ->defaultSort('period_start', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVatPeriods::route('/'),
            'create' => CreateVatPeriod::route('/create'),
            'edit' => EditVatPeriod::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            VatReportsRelationManager::class,
        ];
    }
}
