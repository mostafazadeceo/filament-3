<?php

namespace Haida\FilamentMarketplaceConnectors\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceConnectorResource\Pages\CreateMarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceConnectorResource\Pages\EditMarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceConnectorResource\Pages\ListMarketplaceConnectors;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Illuminate\Database\Eloquent\Model;

class MarketplaceConnectorResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MarketplaceConnector::class;

    protected static ?string $modelLabel = 'اتصال مارکت‌پلیس';

    protected static ?string $pluralModelLabel = 'اتصال‌های مارکت‌پلیس';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'مارکت‌پلیس';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['marketplace.connectors.manage', 'marketplace.connectors.sync']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['marketplace.connectors.manage', 'marketplace.connectors.sync'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('marketplace.connectors.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('marketplace.connectors.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('marketplace.connectors.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('provider_key')
                    ->label('سرویس')
                    ->options(fn () => collect(array_keys((array) config('filament-marketplace-connectors.providers', [])))
                        ->mapWithKeys(fn ($key) => [$key => $key])
                        ->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->maxLength(255)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('inactive')
                    ->required(),
                Textarea::make('config')
                    ->label('پیکربندی (JSON)')
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
                Textarea::make('metadata')
                    ->label('متادیتا (JSON)')
                    ->rows(3)
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
                TextColumn::make('provider_key')
                    ->label('سرویس')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketplaceConnectors::route('/'),
            'create' => CreateMarketplaceConnector::route('/create'),
            'edit' => EditMarketplaceConnector::route('/{record}/edit'),
        ];
    }
}
