<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCashCountResource\Pages\CreatePettyCashCashCount;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCashCountResource\Pages\EditPettyCashCashCount;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCashCountResource\Pages\ListPettyCashCashCounts;
use Haida\FilamentPettyCashIr\Models\PettyCashCashCount;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Illuminate\Database\Eloquent\Builder;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class PettyCashCashCountResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.controls.cash_count';

    protected static ?string $model = PettyCashCashCount::class;

    protected static ?string $modelLabel = 'شمارش نقدی';

    protected static ?string $pluralModelLabel = 'شمارش‌های نقدی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    protected static array $eagerLoad = ['fund'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(function () {
                        $tenantId = TenantContext::getTenantId();

                        return AccountingCompany::query()
                            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required(),
                Select::make('fund_id')
                    ->label('تنخواه')
                    ->options(function () {
                        $tenantId = TenantContext::getTenantId();

                        return PettyCashFund::query()
                            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required(),
                DatePicker::make('count_date')
                    ->label('تاریخ شمارش')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'submitted' => 'ارسال‌شده',
                        'approved' => 'تأیید‌شده',
                    ])
                    ->default('draft'),
                TextInput::make('expected_balance')
                    ->label('موجودی مورد انتظار')
                    ->numeric()
                    ->required(),
                TextInput::make('counted_balance')
                    ->label('موجودی شمارش‌شده')
                    ->numeric()
                    ->required(),
                TextInput::make('variance')
                    ->label('مغایرت')
                    ->numeric()
                    ->disabled(),
                Textarea::make('notes')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fund.name')->label('تنخواه')->searchable(),
                TextColumn::make('count_date')->label('تاریخ')->jalaliDate(),
                TextColumn::make('expected_balance')->label('مورد انتظار'),
                TextColumn::make('counted_balance')->label('شمارش‌شده'),
                TextColumn::make('variance')->label('مغایرت')->badge(),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('count_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashCashCounts::route('/'),
            'create' => CreatePettyCashCashCount::route('/create'),
            'edit' => EditPettyCashCashCount::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery()->with(static::$eagerLoad));
    }
}
