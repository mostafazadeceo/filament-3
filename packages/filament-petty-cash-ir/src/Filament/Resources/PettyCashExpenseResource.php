<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Application\Services\PettyCashAiService;
use Haida\FilamentPettyCashIr\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource\Pages\CreatePettyCashExpense;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource\Pages\EditPettyCashExpense;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource\Pages\ListPettyCashExpenses;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource\RelationManagers\ExpenseAttachmentsRelationManager;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Services\PettyCashPostingService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Database\Eloquent\Builder;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\Party;

class PettyCashExpenseResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.expense';

    protected static ?string $model = PettyCashExpense::class;

    protected static ?string $modelLabel = 'هزینه تنخواه';

    protected static ?string $pluralModelLabel = 'هزینه‌های تنخواه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

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
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(function () {
                        $tenantId = TenantContext::getTenantId();

                        return AccountingBranch::query()
                            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->nullable(),
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
                DatePicker::make('expense_date')
                    ->label('تاریخ هزینه')
                    ->required(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                TextInput::make('currency')
                    ->label('واحد پول')
                    ->default('IRR')
                    ->maxLength(10),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(PettyCashStatuses::expenseOptions())
                    ->default(PettyCashStatuses::EXPENSE_DRAFT),
                TextInput::make('reference')
                    ->label('شماره/ارجاع')
                    ->maxLength(64),
                TextInput::make('payee_name')
                    ->label('دریافت‌کننده')
                    ->maxLength(255),
                Select::make('accounting_party_id')
                    ->label('طرف حساب')
                    ->options(function () {
                        $tenantId = TenantContext::getTenantId();

                        return Party::query()
                            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->nullable(),
                Toggle::make('receipt_required')
                    ->label('رسید الزامی است')
                    ->default(true),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
                Section::make('پیشنهاد هوشمند')
                    ->description('پیشنهادهای هوشمند فقط در صورت فعال بودن هوش مصنوعی نمایش داده می‌شوند.')
                    ->schema([
                        Textarea::make('ai_suggestion_summary')
                            ->label('خلاصه پیشنهاد')
                            ->rows(2)
                            ->disabled()
                            ->dehydrated(false)
                            ->default(fn (?PettyCashExpense $record) => app(PettyCashAiService::class)->summaryForExpense($record)),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fund.name')->label('تنخواه')->searchable(),
                TextColumn::make('category.name')->label('دسته هزینه'),
                TextColumn::make('amount')->label('مبلغ')->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('expense_date')->label('تاریخ')->jalaliDate(),
            ])
            ->actions([
                Action::make('submit')
                    ->label('ارسال')
                    ->visible(fn (PettyCashExpense $record) => $record->status === PettyCashStatuses::EXPENSE_DRAFT)
                    ->authorize(fn (PettyCashExpense $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashExpense $record) => app(PettyCashPostingService::class)
                        ->submitExpense($record, auth()->id())),
                Action::make('approve')
                    ->label('تأیید')
                    ->visible(fn (PettyCashExpense $record) => $record->status === PettyCashStatuses::EXPENSE_SUBMITTED)
                    ->authorize(fn (PettyCashExpense $record) => auth()->user()?->can('approve', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashExpense $record) => app(PettyCashPostingService::class)
                        ->approveExpense($record, auth()->id())),
                Action::make('reject')
                    ->label('رد')
                    ->visible(fn (PettyCashExpense $record) => in_array($record->status, [PettyCashStatuses::EXPENSE_SUBMITTED, PettyCashStatuses::EXPENSE_APPROVED], true))
                    ->authorize(fn (PettyCashExpense $record) => auth()->user()?->can('reject', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashExpense $record) => app(PettyCashPostingService::class)
                        ->rejectExpense($record, auth()->id())),
                Action::make('post')
                    ->label('پرداخت')
                    ->visible(fn (PettyCashExpense $record) => $record->status === PettyCashStatuses::EXPENSE_APPROVED)
                    ->authorize(fn (PettyCashExpense $record) => auth()->user()?->can('post', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashExpense $record) => app(PettyCashPostingService::class)
                        ->postExpense($record, auth()->id())),
            ])
            ->defaultSort('expense_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ExpenseAttachmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashExpenses::route('/'),
            'create' => CreatePettyCashExpense::route('/create'),
            'edit' => EditPettyCashExpense::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery()->with(static::$eagerLoad));
    }
}
