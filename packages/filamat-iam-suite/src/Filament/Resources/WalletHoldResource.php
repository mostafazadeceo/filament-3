<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\WalletHoldResource\Pages\ListWalletHolds;
use Filamat\IamSuite\Models\WalletHold;
use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WalletHoldResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'wallet';

    protected static ?string $model = WalletHold::class;

    protected static ?string $navigationLabel = 'هولدها';

    protected static ?string $pluralModelLabel = 'هولدها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lock-closed';

    protected static string|\UnitEnum|null $navigationGroup = 'کیف پول';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (TenantContext::shouldBypass()) {
            return $query;
        }

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            return $query;
        }

        return $query->whereHas('wallet', function (Builder $builder) use ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('wallet.currency')->label('ارز'),
                TextColumn::make('wallet.user.name')->label('کاربر'),
                TextColumn::make('amount')->label('مبلغ')->numeric(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'released' => 'آزاد شده',
                        'captured' => 'تسویه شده',
                        default => $state,
                    }),
                TextColumn::make('expires_at')->label('انقضا'),
            ])
            ->actions([
                Action::make('capture')
                    ->label('تسویه')
                    ->visible(fn () => IamAuthorization::allows('wallet.manage'))
                    ->form([
                        \Filament\Forms\Components\TextInput::make('idempotency_key')->label('کلید یکتا')->required(),
                    ])
                    ->action(function (WalletHold $record, array $data) {
                        app(WalletService::class)->captureHold($record, (string) $data['idempotency_key']);
                    }),
                Action::make('release')
                    ->label('آزادسازی')
                    ->visible(fn () => IamAuthorization::allows('wallet.manage'))
                    ->form([
                        \Filament\Forms\Components\TextInput::make('idempotency_key')->label('کلید یکتا')->required(),
                    ])
                    ->action(function (WalletHold $record, array $data) {
                        app(WalletService::class)->releaseHold($record, (string) $data['idempotency_key']);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWalletHolds::route('/'),
        ];
    }
}
