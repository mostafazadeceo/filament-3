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
use Haida\FilamentProvidersEsimGo\Resources\EsimGoEsimResource\Pages\ListEsimGoEsims;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoEsimResource\Pages\ViewEsimGoEsim;
use Haida\ProvidersEsimGoCore\Models\EsimGoEsim;

class EsimGoEsimResource extends IamResource
{
    protected static ?string $model = EsimGoEsim::class;

    protected static ?string $permissionPrefix = 'esim_go.fulfillment';

    protected static ?string $modelLabel = 'eSIM';

    protected static ?string $pluralModelLabel = 'eSIMها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'eSIMها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('iccid')->label('ICCID')->searchable(),
                TextColumn::make('matching_id')->label('Matching ID'),
                TextColumn::make('smdp_address')->label('SM-DP+'),
                TextColumn::make('state')->label('وضعیت'),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEsimGoEsims::route('/'),
            'view' => ViewEsimGoEsim::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('اطلاعات eSIM')
                ->schema([
                    TextEntry::make('iccid')->label('ICCID'),
                    TextEntry::make('matching_id')->label('Matching ID'),
                    TextEntry::make('smdp_address')->label('SM-DP+'),
                    TextEntry::make('state')->label('وضعیت'),
                    TextEntry::make('first_installed_at')->label('اولین نصب')->jalaliDateTime(),
                    TextEntry::make('last_refreshed_at')->label('آخرین بروزرسانی')->jalaliDateTime(),
                    TextEntry::make('external_ref')->label('ارجاع خارجی'),
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
