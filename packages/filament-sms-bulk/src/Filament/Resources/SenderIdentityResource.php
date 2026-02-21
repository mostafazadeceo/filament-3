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
use Haida\SmsBulk\Filament\Resources\SenderIdentityResource\Pages\CreateSenderIdentity;
use Haida\SmsBulk\Filament\Resources\SenderIdentityResource\Pages\EditSenderIdentity;
use Haida\SmsBulk\Filament\Resources\SenderIdentityResource\Pages\ListSenderIdentities;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Models\SmsBulkSenderIdentity;

class SenderIdentityResource extends Resource
{
    protected static ?string $model = SmsBulkSenderIdentity::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.senders');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.sender.view', 'sms-bulk.sender.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('provider_connection_id')
                    ->label(__('filament-sms-bulk::messages.nav.connections'))
                    ->options(fn () => SmsBulkProviderConnection::query()->pluck('display_name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('sender')->label(__('filament-sms-bulk::messages.fields.sender'))->required(),
                TextInput::make('label')->label(__('filament-sms-bulk::messages.fields.name')),
                Select::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->options(['active' => 'active', 'inactive' => 'inactive'])->default('active'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('sender')->label(__('filament-sms-bulk::messages.fields.sender'))->searchable(),
            TextColumn::make('label')->label(__('filament-sms-bulk::messages.fields.name')),
            TextColumn::make('providerConnection.display_name')->label(__('filament-sms-bulk::messages.nav.connections')),
            TextColumn::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->badge(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSenderIdentities::route('/'),
            'create' => CreateSenderIdentity::route('/create'),
            'edit' => EditSenderIdentity::route('/{record}/edit'),
        ];
    }
}
