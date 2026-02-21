<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\ProviderConnectionResource\Pages\CreateProviderConnection;
use Haida\SmsBulk\Filament\Resources\ProviderConnectionResource\Pages\EditProviderConnection;
use Haida\SmsBulk\Filament\Resources\ProviderConnectionResource\Pages\ListProviderConnections;
use Haida\SmsBulk\Jobs\TestProviderConnectionJob;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;

class ProviderConnectionResource extends Resource
{
    protected static ?string $model = SmsBulkProviderConnection::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-plug';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.connections');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.connection.view', 'sms-bulk.connection.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('filament-sms-bulk::messages.nav.connections'))
                ->schema([
                    Select::make('provider')
                        ->label(__('filament-sms-bulk::messages.fields.provider'))
                        ->options(['ippanel_edge' => 'IPPanel Edge'])
                        ->required(),
                    TextInput::make('display_name')
                        ->label(__('filament-sms-bulk::messages.fields.display_name'))
                        ->required()
                        ->maxLength(150),
                    TextInput::make('base_url_override')
                        ->label(__('filament-sms-bulk::messages.fields.base_url_override'))
                        ->url(),
                    TextInput::make('default_sender')
                        ->label(__('filament-sms-bulk::messages.fields.default_sender')),
                    TextInput::make('encrypted_token')
                        ->label(__('filament-sms-bulk::messages.fields.token'))
                        ->password()
                        ->revealable()
                        ->dehydrated(fn ($state): bool => filled($state)),
                    Select::make('status')
                        ->label(__('filament-sms-bulk::messages.fields.status'))
                        ->options(['active' => 'active', 'inactive' => 'inactive', 'failing' => 'failing'])
                        ->default('active')
                        ->required(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')->label(__('filament-sms-bulk::messages.fields.display_name'))->searchable(),
                TextColumn::make('provider')->label(__('filament-sms-bulk::messages.fields.provider'))->badge(),
                TextColumn::make('default_sender')->label(__('filament-sms-bulk::messages.fields.default_sender')),
                TextColumn::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->badge(),
                TextColumn::make('last_credit_snapshot')->label(__('filament-sms-bulk::messages.widgets.credit')),
                TextColumn::make('last_tested_at')->label(__('filament-sms-bulk::messages.fields.last_tested_at'))->jalaliDateTime(),
            ])
            ->actions([
                Action::make('test_connection')
                    ->label(__('filament-sms-bulk::messages.actions.test_connection'))
                    ->action(fn (SmsBulkProviderConnection $record) => TestProviderConnectionJob::dispatch($record->tenant_id, (int) $record->getKey())),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProviderConnections::route('/'),
            'create' => CreateProviderConnection::route('/create'),
            'edit' => EditProviderConnection::route('/{record}/edit'),
        ];
    }
}
