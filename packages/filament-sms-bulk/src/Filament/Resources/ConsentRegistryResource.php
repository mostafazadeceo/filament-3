<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\ConsentRegistryResource\Pages\CreateConsent;
use Haida\SmsBulk\Filament\Resources\ConsentRegistryResource\Pages\EditConsent;
use Haida\SmsBulk\Filament\Resources\ConsentRegistryResource\Pages\ListConsents;
use Haida\SmsBulk\Models\SmsBulkConsentRegistry;

class ConsentRegistryResource extends Resource
{
    protected static ?string $model = SmsBulkConsentRegistry::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.consent');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.suppression.view', 'sms-bulk.suppression.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('msisdn')->label(__('filament-sms-bulk::messages.fields.msisdn'))->required(),
                Select::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->options([
                    'opt_in' => 'opt_in',
                    'opt_out' => 'opt_out',
                    'unknown' => 'unknown',
                ])->required(),
                TextInput::make('source')->label(__('filament-sms-bulk::messages.fields.source')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('msisdn')->label(__('filament-sms-bulk::messages.fields.msisdn'))->searchable(),
            TextColumn::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->badge(),
            TextColumn::make('source')->label(__('filament-sms-bulk::messages.fields.source')),
            TextColumn::make('updated_at')->label(__('filament-sms-bulk::messages.fields.updated_at'))->jalaliDateTime(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListConsents::route('/'),
            'create' => CreateConsent::route('/create'),
            'edit' => EditConsent::route('/{record}/edit'),
        ];
    }
}
