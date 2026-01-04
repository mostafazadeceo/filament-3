<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Enums\CryptoPayoutStatus;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoPayoutResource\Pages\ListCryptoPayouts;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;
use Haida\FilamentCryptoGateway\Services\PayoutService;
use Illuminate\Support\Facades\Auth;

class CryptoPayoutResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.payouts';

    protected static ?string $model = CryptoPayout::class;

    protected static ?string $modelLabel = 'برداشت رمزارز';

    protected static ?string $pluralModelLabel = 'برداشت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-right';

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
                TextColumn::make('network')
                    ->label('شبکه')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('to_address')
                    ->label('آدرس مقصد')
                    ->limit(18)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('txid')
                    ->label('TXID')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approved_at')
                    ->label('تایید')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('تایید و ارسال')
                    ->requiresConfirmation()
                    ->action(fn (CryptoPayout $record) => app(PayoutService::class)->approve($record, Auth::id()))
                    ->visible(fn (CryptoPayout $record) => $record->status === CryptoPayoutStatus::PendingApproval->value
                        && IamAuthorization::allows('crypto.payouts.approve', IamAuthorization::resolveTenantFromRecord($record))),
                Action::make('reject')
                    ->label('رد')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (CryptoPayout $record) => app(PayoutService::class)->reject($record, Auth::id()))
                    ->visible(fn (CryptoPayout $record) => $record->status === CryptoPayoutStatus::PendingApproval->value
                        && IamAuthorization::allows('crypto.payouts.approve', IamAuthorization::resolveTenantFromRecord($record))),
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
            'index' => ListCryptoPayouts::route('/'),
        ];
    }
}
