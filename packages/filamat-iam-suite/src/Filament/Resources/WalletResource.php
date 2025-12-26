<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\WalletResource\Pages\CreateWallet;
use Filamat\IamSuite\Filament\Resources\WalletResource\Pages\EditWallet;
use Filamat\IamSuite\Filament\Resources\WalletResource\Pages\ListWallets;
use Filamat\IamSuite\Filament\Resources\WalletResource\RelationManagers\WalletHoldsRelationManager;
use Filamat\IamSuite\Filament\Resources\WalletResource\RelationManagers\WalletTransactionsRelationManager;
use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'wallet';

    protected static ?string $model = Wallet::class;

    protected static ?string $navigationLabel = 'کیف پول‌ها';

    protected static ?string $pluralModelLabel = 'کیف پول‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static string|\UnitEnum|null $navigationGroup = 'کیف پول';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('user_id')
                    ->label('کاربر')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('currency')->label('ارز')->required(),
                TextInput::make('balance')->label('موجودی')->numeric(),
                TextInput::make('status')->label('وضعیت')->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('balance')->label('موجودی')->numeric(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                Action::make('credit')
                    ->label('افزایش موجودی')
                    ->form([
                        TextInput::make('amount')->label('مبلغ')->numeric()->required(),
                        TextInput::make('idempotency_key')->label('کلید یکتا')->required(),
                        KeyValue::make('meta')->label('متادیتا')->nullable(),
                    ])
                    ->visible(fn () => IamAuthorization::allowsAny(['wallet.manage', 'wallet.credit']))
                    ->action(function (Wallet $record, array $data) {
                        app(WalletService::class)->credit(
                            $record,
                            (float) $data['amount'],
                            (string) $data['idempotency_key'],
                            $data['meta'] ?? []
                        );
                    }),
                Action::make('debit')
                    ->label('کاهش موجودی')
                    ->form([
                        TextInput::make('amount')->label('مبلغ')->numeric()->required(),
                        TextInput::make('idempotency_key')->label('کلید یکتا')->required(),
                        KeyValue::make('meta')->label('متادیتا')->nullable(),
                    ])
                    ->visible(fn () => IamAuthorization::allowsAny(['wallet.manage', 'wallet.debit']))
                    ->action(function (Wallet $record, array $data) {
                        app(WalletService::class)->debit(
                            $record,
                            (float) $data['amount'],
                            (string) $data['idempotency_key'],
                            $data['meta'] ?? []
                        );
                    }),
                Action::make('hold')
                    ->label('هولد')
                    ->form([
                        TextInput::make('amount')->label('مبلغ')->numeric()->required(),
                        TextInput::make('reason')->label('دلیل')->nullable(),
                        TextInput::make('idempotency_key')->label('کلید یکتا')->nullable(),
                        KeyValue::make('meta')->label('متادیتا')->nullable(),
                    ])
                    ->visible(fn () => IamAuthorization::allowsAny(['wallet.manage', 'wallet.hold']))
                    ->action(function (Wallet $record, array $data) {
                        app(WalletService::class)->hold(
                            $record,
                            (float) $data['amount'],
                            (string) ($data['reason'] ?? ''),
                            $data['meta'] ?? [],
                            $data['idempotency_key'] ?? null
                        );
                    }),
                Action::make('transfer')
                    ->label('انتقال داخلی')
                    ->form([
                        Select::make('target_wallet_id')
                            ->label('کیف پول مقصد')
                            ->options(function (Wallet $record) {
                                $query = Wallet::query()->with('user')
                                    ->where('currency', $record->currency);
                                $tenantId = TenantContext::getTenantId();
                                if ($tenantId) {
                                    $query->where('tenant_id', $tenantId);
                                }

                                return $query
                                    ->where('id', '!=', $record->getKey())
                                    ->get()
                                    ->mapWithKeys(fn (Wallet $wallet) => [
                                        $wallet->getKey() => "{$wallet->getKey()} - {$wallet->user?->name}",
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->required(),
                        TextInput::make('amount')->label('مبلغ')->numeric()->required(),
                        TextInput::make('idempotency_key')->label('کلید یکتا')->required(),
                        KeyValue::make('meta')->label('متادیتا')->nullable(),
                    ])
                    ->visible(fn () => IamAuthorization::allowsAny(['wallet.manage', 'wallet.transfer']))
                    ->action(function (Wallet $record, array $data) {
                        $target = Wallet::query()->findOrFail($data['target_wallet_id']);
                        app(WalletService::class)->transfer(
                            $record,
                            $target,
                            (float) $data['amount'],
                            (string) $data['idempotency_key'],
                            $data['meta'] ?? []
                        );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWallets::route('/'),
            'create' => CreateWallet::route('/create'),
            'edit' => EditWallet::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            WalletTransactionsRelationManager::class,
            WalletHoldsRelationManager::class,
        ];
    }
}
