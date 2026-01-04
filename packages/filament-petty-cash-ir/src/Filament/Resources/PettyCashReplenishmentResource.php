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
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReplenishmentResource\Pages\CreatePettyCashReplenishment;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReplenishmentResource\Pages\EditPettyCashReplenishment;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashReplenishmentResource\Pages\ListPettyCashReplenishments;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Services\PettyCashPostingService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Database\Eloquent\Builder;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;

class PettyCashReplenishmentResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.replenishment';

    protected static ?string $model = PettyCashReplenishment::class;

    protected static ?string $modelLabel = 'تغذیه تنخواه';

    protected static ?string $pluralModelLabel = 'تغذیه‌های تنخواه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

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
                DatePicker::make('request_date')
                    ->label('تاریخ درخواست')
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
                    ->options(PettyCashStatuses::replenishmentOptions())
                    ->default(PettyCashStatuses::REPLENISHMENT_DRAFT),
                Select::make('source_treasury_account_id')
                    ->label('حساب خزانه منبع')
                    ->options(function () {
                        $tenantId = TenantContext::getTenantId();

                        return TreasuryAccount::query()
                            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->nullable(),
                Textarea::make('description')
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
                TextColumn::make('amount')->label('مبلغ')->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('request_date')->label('تاریخ')->jalaliDate(),
            ])
            ->actions([
                Action::make('submit')
                    ->label('ارسال')
                    ->visible(fn (PettyCashReplenishment $record) => $record->status === PettyCashStatuses::REPLENISHMENT_DRAFT)
                    ->authorize(fn (PettyCashReplenishment $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashReplenishment $record) => app(PettyCashPostingService::class)
                        ->submitReplenishment($record, auth()->id())),
                Action::make('approve')
                    ->label('تأیید')
                    ->visible(fn (PettyCashReplenishment $record) => $record->status === PettyCashStatuses::REPLENISHMENT_SUBMITTED)
                    ->authorize(fn (PettyCashReplenishment $record) => auth()->user()?->can('approve', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashReplenishment $record) => app(PettyCashPostingService::class)
                        ->approveReplenishment($record, auth()->id())),
                Action::make('reject')
                    ->label('رد')
                    ->visible(fn (PettyCashReplenishment $record) => in_array($record->status, [PettyCashStatuses::REPLENISHMENT_SUBMITTED, PettyCashStatuses::REPLENISHMENT_APPROVED], true))
                    ->authorize(fn (PettyCashReplenishment $record) => auth()->user()?->can('reject', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashReplenishment $record) => app(PettyCashPostingService::class)
                        ->rejectReplenishment($record, auth()->id())),
                Action::make('post')
                    ->label('پرداخت')
                    ->visible(fn (PettyCashReplenishment $record) => $record->status === PettyCashStatuses::REPLENISHMENT_APPROVED)
                    ->authorize(fn (PettyCashReplenishment $record) => auth()->user()?->can('post', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashReplenishment $record) => app(PettyCashPostingService::class)
                        ->postReplenishment($record, auth()->id())),
            ])
            ->defaultSort('request_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashReplenishments::route('/'),
            'create' => CreatePettyCashReplenishment::route('/create'),
            'edit' => EditPettyCashReplenishment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery()->with(static::$eagerLoad));
    }
}
