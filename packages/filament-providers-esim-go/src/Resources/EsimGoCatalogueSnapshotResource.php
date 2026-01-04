<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoCatalogueSnapshotResource\Pages\ListEsimGoCatalogueSnapshots;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoCatalogueSnapshotResource\Pages\ViewEsimGoCatalogueSnapshot;
use Haida\ProvidersEsimGoCore\Models\EsimGoCatalogueSnapshot;

class EsimGoCatalogueSnapshotResource extends IamResource
{
    protected static ?string $model = EsimGoCatalogueSnapshot::class;

    protected static ?string $permissionPrefix = 'esim_go.catalogue';

    protected static ?string $modelLabel = 'اسنپ‌شات کاتالوگ';

    protected static ?string $pluralModelLabel = 'اسنپ‌شات‌های کاتالوگ';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'اسنپ‌شات‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fetched_at')->label('زمان دریافت')->jalaliDateTime()->sortable(),
                TextColumn::make('hash')->label('هش')->toggleable(),
                TextColumn::make('source_version')->label('نسخه منبع'),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
            ])
            ->defaultSort('fetched_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEsimGoCatalogueSnapshots::route('/'),
            'view' => ViewEsimGoCatalogueSnapshot::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات اسنپ‌شات')
                ->schema([
                    TextEntry::make('fetched_at')->label('زمان دریافت')->jalaliDateTime(),
                    TextEntry::make('count')
                        ->label('تعداد آیتم‌ها')
                        ->getStateUsing(fn (EsimGoCatalogueSnapshot $record) => data_get($record->payload, 'count', '-')),
                    TextEntry::make('hash')->label('هش'),
                    TextEntry::make('source_version')
                        ->label('نسخه منبع')
                        ->getStateUsing(fn (EsimGoCatalogueSnapshot $record) => $record->source_version ?? 'v2.5'),
                    TextEntry::make('filters')
                        ->label('فیلترها')
                        ->getStateUsing(fn (EsimGoCatalogueSnapshot $record) => $record->filters
                            ? json_encode($record->filters, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                            : '-')
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-providers-esim-go.navigation.group', 'Providerها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-providers-esim-go.navigation.sort', 30);
    }
}
