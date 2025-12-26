<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WebhookResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebhookDeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    protected static ?string $title = 'ارسال‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('idempotency_key')->label('کلید یکتا')->limit(12),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'queued' => 'در صف',
                        'delivered' => 'تحویل شده',
                        'failed' => 'ناموفق',
                        'skipped' => 'رد شده',
                        default => $state,
                    }),
                TextColumn::make('attempts')->label('تلاش'),
                TextColumn::make('last_attempt_at')->label('آخرین تلاش'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }
}
