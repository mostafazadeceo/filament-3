<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\PurchaseInvoiceResource\Pages\CreatePurchaseInvoice;
use Vendor\FilamentAccountingIr\Filament\Resources\PurchaseInvoiceResource\Pages\EditPurchaseInvoice;
use Vendor\FilamentAccountingIr\Filament\Resources\PurchaseInvoiceResource\Pages\ListPurchaseInvoices;
use Vendor\FilamentAccountingIr\Filament\Resources\PurchaseInvoiceResource\RelationManagers\PurchaseInvoiceLinesRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\Party;
use Vendor\FilamentAccountingIr\Models\PurchaseInvoice;
use Vendor\FilamentAccountingIr\Services\PurchaseInvoiceService;

class PurchaseInvoiceResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = PurchaseInvoice::class;

    protected static ?string $modelLabel = 'فاکتور خرید';

    protected static ?string $pluralModelLabel = 'فاکتورهای خرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $navigationLabel = 'فاکتورهای خرید';

    protected static string|\UnitEnum|null $navigationGroup = 'خرید و پرداختنی‌ها';

    protected static ?int $navigationSort = 1;

    protected static array $eagerLoad = ['company', 'party'];

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
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('fiscal_year_id')
                    ->label('سال مالی')
                    ->options(fn () => FiscalYear::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('party_id')
                    ->label('تامین‌کننده')
                    ->options(fn () => Party::query()->where('party_type', 'supplier')->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('invoice_no')
                    ->label('شماره فاکتور')
                    ->required()
                    ->maxLength(64),
                DatePicker::make('invoice_date')
                    ->label('تاریخ فاکتور')
                    ->required(),
                DatePicker::make('due_date')
                    ->label('سررسید'),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'received' => 'دریافت شده',
                        'paid' => 'تسویه شده',
                    ])
                    ->default('draft'),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default('IRR')
                    ->maxLength(8),
                TextInput::make('exchange_rate')
                    ->label('نرخ تسعیر')
                    ->numeric()
                    ->minValue(0),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_no')->label('شماره')->searchable()->sortable(),
                TextColumn::make('invoice_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('party.name')->label('تامین‌کننده')->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('total')->label('مبلغ')->numeric(decimalPlaces: 0),
            ])
            ->actions([
                TableAction::make('receive')
                    ->label('ثبت')
                    ->visible(fn (PurchaseInvoice $record) => $record->status === 'draft')
                    ->action(function (PurchaseInvoice $record): void {
                        app(PurchaseInvoiceService::class)->receive($record);
                        Notification::make()->title('فاکتور ثبت شد.')->success()->send();
                    }),
            ])
            ->defaultSort('invoice_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PurchaseInvoiceLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseInvoices::route('/'),
            'create' => CreatePurchaseInvoice::route('/create'),
            'edit' => EditPurchaseInvoice::route('/{record}/edit'),
        ];
    }
}
