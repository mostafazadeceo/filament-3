<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashSettlementResource\Pages\CreatePettyCashSettlement;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashSettlementResource\Pages\EditPettyCashSettlement;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashSettlementResource\Pages\ListPettyCashSettlements;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashSettlementResource\RelationManagers\SettlementItemsRelationManager;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Services\PettyCashPostingService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class PettyCashSettlementResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.settlement';

    protected static ?string $model = PettyCashSettlement::class;

    protected static ?string $modelLabel = 'تسویه تنخواه';

    protected static ?string $pluralModelLabel = 'تسویه‌های تنخواه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

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
                Select::make('fund_id')
                    ->label('تنخواه')
                    ->options(fn () => PettyCashFund::query()->pluck('name', 'id')->toArray())
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
                    ->options(PettyCashStatuses::settlementOptions())
                    ->default(PettyCashStatuses::SETTLEMENT_DRAFT),
                TextInput::make('total_expenses')
                    ->label('جمع هزینه‌ها')
                    ->numeric()
                    ->disabled(),
                TextInput::make('total_replenished')
                    ->label('جمع تغذیه‌ها')
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
                TextColumn::make('total_expenses')->label('جمع هزینه‌ها'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->actions([
                Action::make('submit')
                    ->label('ارسال')
                    ->visible(fn (PettyCashSettlement $record) => $record->status === PettyCashStatuses::SETTLEMENT_DRAFT)
                    ->authorize(fn (PettyCashSettlement $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashSettlement $record) => app(PettyCashPostingService::class)
                        ->submitSettlement($record, auth()->id())),
                Action::make('approve')
                    ->label('تأیید')
                    ->visible(fn (PettyCashSettlement $record) => $record->status === PettyCashStatuses::SETTLEMENT_SUBMITTED)
                    ->authorize(fn (PettyCashSettlement $record) => auth()->user()?->can('approve', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashSettlement $record) => app(PettyCashPostingService::class)
                        ->approveSettlement($record, auth()->id())),
                Action::make('post')
                    ->label('قطعی‌سازی')
                    ->visible(fn (PettyCashSettlement $record) => $record->status === PettyCashStatuses::SETTLEMENT_APPROVED)
                    ->authorize(fn (PettyCashSettlement $record) => auth()->user()?->can('post', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PettyCashSettlement $record) => app(PettyCashPostingService::class)
                        ->postSettlement($record, auth()->id())),
            ])
            ->defaultSort('period_start', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            SettlementItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashSettlements::route('/'),
            'create' => CreatePettyCashSettlement::route('/create'),
            'edit' => EditPettyCashSettlement::route('/{record}/edit'),
        ];
    }
}
