<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\WalletTransactionResource\Pages\ListWalletTransactions;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WalletTransactionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'wallet';

    protected static ?string $model = WalletTransaction::class;

    protected static ?string $navigationLabel = 'تراکنش‌ها';

    protected static ?string $pluralModelLabel = 'تراکنش‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

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
                TextColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'credit' => 'افزایش',
                        'debit' => 'کاهش',
                        'transfer_out' => 'انتقال خروجی',
                        'transfer_in' => 'انتقال ورودی',
                        'capture' => 'تسویه',
                        'release' => 'آزادسازی',
                        default => $state,
                    }),
                TextColumn::make('amount')->label('مبلغ')->numeric(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'posted' => 'ثبت‌شده',
                        'pending' => 'در انتظار',
                        'failed' => 'ناموفق',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWalletTransactions::route('/'),
        ];
    }
}
