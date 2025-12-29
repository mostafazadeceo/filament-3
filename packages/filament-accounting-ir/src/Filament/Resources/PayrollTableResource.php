<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollTableResource\Pages\CreatePayrollTable;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollTableResource\Pages\EditPayrollTable;
use Vendor\FilamentAccountingIr\Filament\Resources\PayrollTableResource\Pages\ListPayrollTables;
use Vendor\FilamentAccountingIr\Models\PayrollTable;

class PayrollTableResource extends Resource
{
    protected static ?string $model = PayrollTable::class;

    protected static ?string $modelLabel = 'جدول حقوق';

    protected static ?string $pluralModelLabel = 'جداول حقوق';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationLabel = 'جداول حقوق';

    protected static string|\UnitEnum|null $navigationGroup = 'حقوق و دستمزد';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('table_type')
                    ->label('نوع جدول')
                    ->options([
                        'minimum_wage' => 'حداقل دستمزد',
                        'tax_brackets' => 'پله‌های مالیات حقوق',
                        'insurance_rates' => 'نرخ بیمه',
                        'benefits' => 'مزایا',
                    ])
                    ->required(),
                DatePicker::make('effective_from')
                    ->label('از تاریخ'),
                DatePicker::make('effective_to')
                    ->label('تا تاریخ'),
                Textarea::make('payload')
                    ->label('داده')
                    ->helperText('ساختار JSON برای جدول‌ها ثبت می‌شود.')
                    ->afterStateHydrated(function (Textarea $component, $state): void {
                        if (is_array($state)) {
                            $component->state(json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => $state ? json_decode((string) $state, true) : [])
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('table_type')->label('نوع')->badge(),
                TextColumn::make('effective_from')->label('از')->jalaliDate(),
                TextColumn::make('effective_to')->label('تا')->jalaliDate(),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollTables::route('/'),
            'create' => CreatePayrollTable::route('/create'),
            'edit' => EditPayrollTable::route('/{record}/edit'),
        ];
    }
}
