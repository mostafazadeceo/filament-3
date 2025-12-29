<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource\Pages\CreateSeasonalReport;
use Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource\Pages\EditSeasonalReport;
use Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource\Pages\ListSeasonalReports;
use Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource\RelationManagers\SeasonalReportLinesRelationManager;
use Vendor\FilamentAccountingIr\Filament\Resources\SeasonalReportResource\RelationManagers\SeasonalSubmissionsRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\SeasonalReport;

class SeasonalReportResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = SeasonalReport::class;

    protected static ?string $modelLabel = 'گزارش فصلی';

    protected static ?string $pluralModelLabel = 'گزارش‌های فصلی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'گزارش‌های فصلی';

    protected static string|\UnitEnum|null $navigationGroup = 'مالیات و انطباق';

    protected static ?int $navigationSort = 5;

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
                DatePicker::make('period_start')
                    ->label('شروع دوره')
                    ->required(),
                DatePicker::make('period_end')
                    ->label('پایان دوره')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'submitted' => 'ارسال شده',
                    ])
                    ->default('draft'),
                TextInput::make('metadata.reference')
                    ->label('کد پیگیری')
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_start')->label('از')->jalaliDate(),
                TextColumn::make('period_end')->label('تا')->jalaliDate(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('period_start', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            SeasonalReportLinesRelationManager::class,
            SeasonalSubmissionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSeasonalReports::route('/'),
            'create' => CreateSeasonalReport::route('/create'),
            'edit' => EditSeasonalReport::route('/{record}/edit'),
        ];
    }
}
