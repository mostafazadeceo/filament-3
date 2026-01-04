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
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReconciliationResource\Pages\CreatePettyCashReconciliation;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReconciliationResource\Pages\EditPettyCashReconciliation;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReconciliationResource\Pages\ListPettyCashReconciliations;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReconciliation;
use Illuminate\Database\Eloquent\Builder;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class PettyCashReconciliationResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.controls.reconcile';

    protected static ?string $model = PettyCashReconciliation::class;

    protected static ?string $modelLabel = 'تطبیق';

    protected static ?string $pluralModelLabel = 'تطبیق‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

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
                DatePicker::make('period_start')
                    ->label('از تاریخ')
                    ->required(),
                DatePicker::make('period_end')
                    ->label('تا تاریخ')
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
                TextInput::make('ledger_balance')
                    ->label('موجودی دفتر')
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
                TextColumn::make('period_start')->label('از')->jalaliDate(),
                TextColumn::make('period_end')->label('تا')->jalaliDate(),
                TextColumn::make('expected_balance')->label('مورد انتظار'),
                TextColumn::make('ledger_balance')->label('دفتر'),
                TextColumn::make('variance')->label('مغایرت')->badge(),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('period_start', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashReconciliations::route('/'),
            'create' => CreatePettyCashReconciliation::route('/create'),
            'edit' => EditPettyCashReconciliation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery()->with(static::$eagerLoad));
    }
}
