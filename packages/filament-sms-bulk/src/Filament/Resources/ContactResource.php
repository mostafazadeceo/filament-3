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
use Haida\SmsBulk\Filament\Resources\ContactResource\Pages\CreateContact;
use Haida\SmsBulk\Filament\Resources\ContactResource\Pages\EditContact;
use Haida\SmsBulk\Filament\Resources\ContactResource\Pages\ListContacts;
use Haida\SmsBulk\Models\SmsBulkContact;
use Haida\SmsBulk\Models\SmsBulkPhonebook;

class ContactResource extends Resource
{
    protected static ?string $model = SmsBulkContact::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.contacts');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.phonebook.view', 'sms-bulk.phonebook.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('phonebook_id')
                    ->label(__('filament-sms-bulk::messages.fields.phonebook'))
                    ->options(fn () => SmsBulkPhonebook::query()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('msisdn')->label(__('filament-sms-bulk::messages.fields.msisdn'))->required(),
                TextInput::make('full_name')->label(__('filament-sms-bulk::messages.fields.name')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('msisdn')->label(__('filament-sms-bulk::messages.fields.msisdn'))->searchable(),
            TextColumn::make('full_name')->label(__('filament-sms-bulk::messages.fields.name'))->searchable(),
            TextColumn::make('phonebook.name')->label(__('filament-sms-bulk::messages.fields.phonebook')),
            TextColumn::make('updated_at')->label(__('filament-sms-bulk::messages.fields.updated_at'))->jalaliDateTime()->sortable(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'create' => CreateContact::route('/create'),
            'edit' => EditContact::route('/{record}/edit'),
        ];
    }
}
