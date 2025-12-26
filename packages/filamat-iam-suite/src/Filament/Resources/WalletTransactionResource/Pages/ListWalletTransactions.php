<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WalletTransactionResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\WalletTransactionResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;

class ListWalletTransactions extends ListRecordsWithCreate
{
    protected static string $resource = WalletTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            Action::make('export')
                ->label('خروجی CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => IamAuthorization::allows('wallet.view'))
                ->action(function () {
                    $records = $this->getTableQueryForExport()->with(['wallet.user'])->get();

                    return response()->streamDownload(function () use ($records) {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, [
                            'id',
                            'wallet_id',
                            'user',
                            'currency',
                            'type',
                            'amount',
                            'status',
                            'created_at',
                        ]);

                        foreach ($records as $record) {
                            fputcsv($handle, [
                                $record->getKey(),
                                $record->wallet_id,
                                $record->wallet?->user?->name,
                                $record->wallet?->currency,
                                $record->type,
                                $record->amount,
                                $record->status,
                                optional($record->created_at)->toDateTimeString(),
                            ]);
                        }

                        fclose($handle);
                    }, 'wallet-transactions.csv');
                }),
        ];
    }
}
