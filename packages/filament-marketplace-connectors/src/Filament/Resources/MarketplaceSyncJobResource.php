<?php

namespace Haida\FilamentMarketplaceConnectors\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceSyncJobResource\Pages\CreateMarketplaceSyncJob;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceSyncJobResource\Pages\EditMarketplaceSyncJob;
use Haida\FilamentMarketplaceConnectors\Filament\Resources\MarketplaceSyncJobResource\Pages\ListMarketplaceSyncJobs;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceSyncJob;
use Illuminate\Database\Eloquent\Model;

class MarketplaceSyncJobResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MarketplaceSyncJob::class;

    protected static ?string $modelLabel = 'همگام‌سازی';

    protected static ?string $pluralModelLabel = 'همگام‌سازی‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

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
        return IamAuthorization::allows('marketplace.connectors.sync');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('marketplace.connectors.sync', IamAuthorization::resolveTenantFromRecord($record));
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
                Select::make('connector_id')
                    ->label('اتصال')
                    ->options(fn () => MarketplaceConnector::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('job_type')
                    ->label('نوع')
                    ->options([
                        'catalog' => 'کاتالوگ',
                        'inventory' => 'موجودی',
                        'orders' => 'سفارش‌ها',
                    ])
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'completed' => 'تکمیل',
                        'failed' => 'ناموفق',
                    ])
                    ->default('pending')
                    ->required(),
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
                TextColumn::make('connector.name')
                    ->label('اتصال'),
                TextColumn::make('job_type')
                    ->label('نوع')
                    ->badge(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('last_run_at')
                    ->label('آخرین اجرا')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMarketplaceSyncJobs::route('/'),
            'create' => CreateMarketplaceSyncJob::route('/create'),
            'edit' => EditMarketplaceSyncJob::route('/{record}/edit'),
        ];
    }
}
