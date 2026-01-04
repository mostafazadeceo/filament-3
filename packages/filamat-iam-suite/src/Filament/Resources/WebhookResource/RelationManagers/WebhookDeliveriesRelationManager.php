<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\WebhookResource\RelationManagers;

use Filamat\IamSuite\Services\WebhookService;
use Filamat\IamSuite\Support\N8nEventCatalog;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Arr;

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
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'queued' => 'در صف',
                        'delivered' => 'تحویل شده',
                        'failed' => 'ناموفق',
                        'skipped' => 'رد شده',
                    ]),
                SelectFilter::make('event')
                    ->label('رویداد')
                    ->options(fn () => $this->resolveEventOptions())
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) {
                            return $query;
                        }

                        return $query->where('request->event', $value);
                    }),
            ])
            ->actions([
                Action::make('retry')
                    ->label('تلاش مجدد')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array($record->status, ['failed', 'skipped'], true))
                    ->action(fn ($record) => app(WebhookService::class)->deliver($record)),
                Action::make('redrive')
                    ->label('ارسال مجدد')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => in_array($record->status, ['failed', 'skipped'], true))
                    ->action(function ($record) {
                        $payload = Arr::except((array) ($record->request ?? []), ['idempotency_key']);
                        app(WebhookService::class)->queue($record->webhook, $payload);
                    }),
            ]);
    }

    /**
     * @return array<string, string>
     */
    protected function resolveEventOptions(): array
    {
        $webhook = $this->getOwnerRecord();
        if (! $webhook) {
            return [];
        }

        if ($webhook->type === 'automation') {
            return N8nEventCatalog::options();
        }

        if (is_array($webhook->events) && $webhook->events !== []) {
            return array_combine($webhook->events, $webhook->events);
        }

        return [];
    }
}
