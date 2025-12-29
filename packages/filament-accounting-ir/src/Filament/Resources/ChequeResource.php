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
use Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource\Pages\CreateCheque;
use Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource\Pages\EditCheque;
use Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource\Pages\ListCheques;
use Vendor\FilamentAccountingIr\Filament\Resources\ChequeResource\RelationManagers\ChequeEventsRelationManager;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\Cheque;
use Vendor\FilamentAccountingIr\Models\Party;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;

class ChequeResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = Cheque::class;

    protected static ?string $modelLabel = 'چک';

    protected static ?string $pluralModelLabel = 'چک‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'چک‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'خزانه و بانک';

    protected static ?int $navigationSort = 3;

    protected static array $eagerLoad = ['company', 'party', 'treasuryAccount'];

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
                Select::make('party_id')
                    ->label('طرف حساب')
                    ->options(fn () => Party::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('treasury_account_id')
                    ->label('حساب خزانه')
                    ->options(fn () => TreasuryAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('direction')
                    ->label('جهت')
                    ->options([
                        'received' => 'دریافتی',
                        'issued' => 'پرداختی',
                    ])
                    ->default('received'),
                TextInput::make('cheque_no')
                    ->label('شماره چک')
                    ->maxLength(64),
                TextInput::make('bank_name')
                    ->label('بانک')
                    ->maxLength(255),
                TextInput::make('branch_name')
                    ->label('شعبه')
                    ->maxLength(255),
                DatePicker::make('due_date')
                    ->label('سررسید'),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->minValue(0),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'issued' => 'ثبت‌شده',
                        'in_bank' => 'نزد بانک',
                        'cleared' => 'وصول شده',
                        'returned' => 'برگشتی',
                    ])
                    ->default('issued'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cheque_no')->label('شماره')->searchable(),
                TextColumn::make('direction')->label('جهت')->badge(),
                TextColumn::make('amount')->label('مبلغ')->numeric(decimalPlaces: 0),
                TextColumn::make('due_date')->label('سررسید')->jalaliDate(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('due_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            ChequeEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCheques::route('/'),
            'create' => CreateCheque::route('/create'),
            'edit' => EditCheque::route('/{record}/edit'),
        ];
    }
}
