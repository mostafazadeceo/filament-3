<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoGateway\Filament\Resources\CryptoWebhookCallResource\Pages\ListCryptoWebhookCalls;
use Haida\FilamentCryptoGateway\Jobs\ProcessWebhookCall;
use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Haida\FilamentCryptoGateway\Services\PlanService;

class CryptoWebhookCallResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.webhooks';

    protected static ?string $model = CryptoWebhookCall::class;

    protected static ?string $modelLabel = 'وبهوک رمزارز';

    protected static ?string $pluralModelLabel = 'وبهوک‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

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
                TextColumn::make('event_id')
                    ->label('شناسه رویداد')
                    ->searchable(),
                IconColumn::make('signature_ok')
                    ->label('امضا')
                    ->boolean(),
                IconColumn::make('ip_ok')
                    ->label('IP')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof \Haida\FilamentCryptoGateway\Enums\CryptoWebhookCallStatus ? $state->value : (string) $state),
                TextColumn::make('received_at')
                    ->label('دریافت')
                    ->jalaliDateTime(),
                TextColumn::make('processed_at')
                    ->label('پردازش')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('reprocess')
                    ->label('بازپردازش')
                    ->requiresConfirmation()
                    ->action(fn (CryptoWebhookCall $record) => ProcessWebhookCall::dispatch($record->getKey()))
                    ->visible(fn () => app(PlanService::class)->allowsFeature(TenantContext::getTenantId(), 'crypto.webhook_replay')),
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
            'index' => ListCryptoWebhookCalls::route('/'),
        ];
    }
}
