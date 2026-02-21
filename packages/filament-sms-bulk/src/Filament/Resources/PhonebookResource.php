<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\PhonebookResource\Pages\CreatePhonebook;
use Haida\SmsBulk\Filament\Resources\PhonebookResource\Pages\EditPhonebook;
use Haida\SmsBulk\Filament\Resources\PhonebookResource\Pages\ListPhonebooks;
use Haida\SmsBulk\Models\SmsBulkPhonebook;

class PhonebookResource extends Resource
{
    protected static ?string $model = SmsBulkPhonebook::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.phonebooks');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.phonebook.view', 'sms-bulk.phonebook.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')->label(__('filament-sms-bulk::messages.fields.name'))->required(),
                Textarea::make('description')->label(__('filament-sms-bulk::messages.fields.description')),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__('filament-sms-bulk::messages.fields.name'))->searchable(),
            TextColumn::make('contacts_count')->counts('contacts')->label(__('filament-sms-bulk::messages.nav.contacts')),
            TextColumn::make('updated_at')->label(__('filament-sms-bulk::messages.fields.updated_at'))->jalaliDateTime()->sortable(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPhonebooks::route('/'),
            'create' => CreatePhonebook::route('/create'),
            'edit' => EditPhonebook::route('/{record}/edit'),
        ];
    }
}
