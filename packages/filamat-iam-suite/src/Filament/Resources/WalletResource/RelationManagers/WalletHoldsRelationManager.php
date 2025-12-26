<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WalletResource\RelationManagers;

use Filamat\IamSuite\Services\WalletService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletHoldsRelationManager extends RelationManager
{
    protected static string $relationship = 'holds';

    protected static ?string $title = 'هولدها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')->label('مبلغ')->numeric(),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('expires_at')->label('انقضا'),
            ])
            ->actions([
                Action::make('capture')
                    ->label('تسویه')
                    ->visible(fn () => IamAuthorization::allows('wallet.manage'))
                    ->form([
                        \Filament\Forms\Components\TextInput::make('idempotency_key')->label('کلید یکتا')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        app(WalletService::class)->captureHold($record, (string) $data['idempotency_key']);
                    }),
                Action::make('release')
                    ->label('آزادسازی')
                    ->visible(fn () => IamAuthorization::allows('wallet.manage'))
                    ->form([
                        \Filament\Forms\Components\TextInput::make('idempotency_key')->label('کلید یکتا')->required(),
                    ])
                    ->action(function ($record, array $data) {
                        app(WalletService::class)->releaseHold($record, (string) $data['idempotency_key']);
                    }),
            ]);
    }
}
