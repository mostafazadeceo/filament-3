<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoInvoiceResource\Pages\ListCryptoInvoices;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Services\InvoiceService;

class CryptoInvoiceResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.invoices';

    protected static ?string $model = CryptoInvoice::class;

    protected static ?string $modelLabel = 'فاکتور رمزارز';

    protected static ?string $pluralModelLabel = 'فاکتورهای رمزارز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('provider')
                    ->label('درگاه')
                    ->searchable(),
                TextColumn::make('order_id')
                    ->label('شناسه سفارش')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('مبلغ'),
                TextColumn::make('currency')
                    ->label('ارز'),
                TextColumn::make('to_currency')
                    ->label('تبدیل')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('network')
                    ->label('شبکه')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('address')
                    ->label('آدرس')
                    ->limit(18)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expires_at')
                    ->label('انقضا')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->actions([
                Action::make('refresh')
                    ->label('به‌روزرسانی')
                    ->requiresConfirmation()
                    ->action(fn (CryptoInvoice $record) => app(InvoiceService::class)->refresh($record))
                    ->visible(fn (CryptoInvoice $record) => static::canEdit($record)),
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
            'index' => ListCryptoInvoices::route('/'),
        ];
    }
}
