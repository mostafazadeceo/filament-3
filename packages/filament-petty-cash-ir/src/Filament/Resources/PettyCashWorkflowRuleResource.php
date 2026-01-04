<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashWorkflowRuleResource\Pages\CreatePettyCashWorkflowRule;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashWorkflowRuleResource\Pages\EditPettyCashWorkflowRule;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashWorkflowRuleResource\Pages\ListPettyCashWorkflowRules;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashWorkflowRule;
use Illuminate\Database\Eloquent\Builder;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class PettyCashWorkflowRuleResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.workflow';

    protected static ?string $model = PettyCashWorkflowRule::class;

    protected static ?string $modelLabel = 'قاعده گردش‌کار';

    protected static ?string $pluralModelLabel = 'قواعد گردش‌کار';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    protected static array $eagerLoad = ['fund', 'category'];

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
                Select::make('transaction_type')
                    ->label('نوع تراکنش')
                    ->options([
                        'expense' => 'هزینه',
                        'replenishment' => 'تغذیه',
                    ])
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
                    ->nullable(),
                Select::make('category_id')
                    ->label('دسته هزینه')
                    ->options(function () {
                        $tenantId = TenantContext::getTenantId();

                        return PettyCashCategory::query()
                            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->nullable(),
                TextInput::make('min_amount')
                    ->label('حداقل مبلغ')
                    ->numeric()
                    ->nullable(),
                TextInput::make('max_amount')
                    ->label('حداکثر مبلغ')
                    ->numeric()
                    ->nullable(),
                TextInput::make('steps_required')
                    ->label('تعداد گام‌های تأیید')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Toggle::make('require_separation')
                    ->label('تفکیک نقش‌ها')
                    ->default(false),
                Select::make('require_receipt')
                    ->label('الزام رسید')
                    ->options([
                        '' => 'پیش‌فرض',
                        1 => 'الزامی',
                        0 => 'غیراجباری',
                    ])
                    ->nullable()
                    ->dehydrateStateUsing(fn ($state) => $state === '' || $state === null ? null : (bool) $state),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_type')->label('نوع'),
                TextColumn::make('fund.name')->label('تنخواه'),
                TextColumn::make('category.name')->label('دسته'),
                TextColumn::make('min_amount')->label('حداقل'),
                TextColumn::make('max_amount')->label('حداکثر'),
                TextColumn::make('steps_required')->label('گام‌ها'),
                IconColumn::make('require_separation')->label('تفکیک')->boolean(),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashWorkflowRules::route('/'),
            'create' => CreatePettyCashWorkflowRule::route('/create'),
            'edit' => EditPettyCashWorkflowRule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery()->with(static::$eagerLoad));
    }
}
