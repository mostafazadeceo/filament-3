<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoicePaymentResource\Pages\ListCryptoInvoicePayments;
use Haida\FilamentCryptoGateway\Models\CryptoInvoicePayment;

class CryptoInvoicePaymentResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.invoice_payments';

    protected static ?string $model = CryptoInvoicePayment::class;

    protected static ?string $modelLabel = 'پرداخت فاکتور';

    protected static ?string $pluralModelLabel = 'پرداخت‌های فاکتور';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('invoice.order_id')
                    ->label('شناسه فاکتور')
                    ->searchable(),
                TextColumn::make('txid')
                    ->label('TXID')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('payer_amount')
                    ->label('مبلغ پرداختی'),
                TextColumn::make('payer_currency')
                    ->label('ارز پرداختی'),
                TextColumn::make('confirmations')
                    ->label('تاییدها'),
                TextColumn::make('seen_at')
                    ->label('مشاهده')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCryptoInvoicePayments::route('/'),
        ];
    }
}
