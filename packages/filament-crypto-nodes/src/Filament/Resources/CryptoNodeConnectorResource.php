<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoNodes\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoNodes\Filament\Resources\CryptoNodeConnectorResource\Pages\CreateCryptoNodeConnector;
use Haida\FilamentCryptoNodes\Filament\Resources\CryptoNodeConnectorResource\Pages\EditCryptoNodeConnector;
use Haida\FilamentCryptoNodes\Filament\Resources\CryptoNodeConnectorResource\Pages\ListCryptoNodeConnectors;
use Haida\FilamentCryptoNodes\Models\CryptoNodeConnector;
use Haida\FilamentCryptoNodes\Services\NodeHealthService;

class CryptoNodeConnectorResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.nodes';

    protected static ?string $model = CryptoNodeConnector::class;

    protected static ?string $modelLabel = 'نود رمزارز';

    protected static ?string $pluralModelLabel = 'نودهای رمزارز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-server';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('type')
                    ->label('نوع نود')
                    ->options([
                        'btcpay' => 'BTCPay Server',
                        'bitcoin_core' => 'Bitcoin Core',
                        'evm' => 'EVM JSON-RPC',
                    ])
                    ->required(),
                TextInput::make('label')
                    ->label('برچسب')
                    ->required()
                    ->maxLength(150),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('inactive')
                    ->required(),
                Textarea::make('config_json')
                    ->label('تنظیمات اتصال (JSON)')
                    ->rows(6)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
                Textarea::make('meta')
                    ->label('متا (JSON)')
                    ->rows(4)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->badge(),
                TextColumn::make('label')
                    ->label('برچسب')
                    ->searchable(),
                IconColumn::make('status')
                    ->label('وضعیت')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->getStateUsing(fn (CryptoNodeConnector $record) => $record->status === 'active'),
                TextColumn::make('last_healthy_at')
                    ->label('آخرین سلامت')
                    ->jalaliDateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->actions([
                Action::make('health_check')
                    ->label('بررسی سلامت')
                    ->requiresConfirmation()
                    ->action(function (CryptoNodeConnector $record): void {
                        $service = app(NodeHealthService::class);
                        $result = $service->checkConnector($record);

                        if (($result['status'] ?? '') === 'ok') {
                            $record->update([
                                'last_healthy_at' => now(),
                                'meta' => array_merge($record->meta ?? [], [
                                    'last_health' => $result,
                                ]),
                            ]);
                        } else {
                            $record->update([
                                'meta' => array_merge($record->meta ?? [], [
                                    'last_health' => $result,
                                ]),
                            ]);
                        }
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCryptoNodeConnectors::route('/'),
            'create' => CreateCryptoNodeConnector::route('/create'),
            'edit' => EditCryptoNodeConnector::route('/{record}/edit'),
        ];
    }
}
