<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\PatternTemplateResource\Pages\CreatePatternTemplate;
use Haida\SmsBulk\Filament\Resources\PatternTemplateResource\Pages\EditPatternTemplate;
use Haida\SmsBulk\Filament\Resources\PatternTemplateResource\Pages\ListPatternTemplates;
use Haida\SmsBulk\Models\SmsBulkPatternTemplate;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;

class PatternTemplateResource extends Resource
{
    protected static ?string $model = SmsBulkPatternTemplate::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.patterns');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.pattern.view', 'sms-bulk.pattern.manage']);
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
                TextInput::make('pattern_code')->label(__('filament-sms-bulk::messages.fields.pattern_code'))->required(),
                TextInput::make('title_translations.fa')->label(__('filament-sms-bulk::messages.fields.name'))->required(),
                KeyValue::make('variables_schema')->label(__('filament-sms-bulk::messages.fields.option_values')),
                Select::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->options(['pending' => 'pending', 'approved' => 'approved', 'rejected' => 'rejected'])->required(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('pattern_code')->label(__('filament-sms-bulk::messages.fields.pattern_code'))->searchable(),
            TextColumn::make('title_translations.fa')->label(__('filament-sms-bulk::messages.fields.name')),
            TextColumn::make('status')->label(__('filament-sms-bulk::messages.fields.status'))->badge(),
            TextColumn::make('last_synced_at')->label(__('filament-sms-bulk::messages.fields.last_synced_at'))->jalaliDateTime(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatternTemplates::route('/'),
            'create' => CreatePatternTemplate::route('/create'),
            'edit' => EditPatternTemplate::route('/{record}/edit'),
        ];
    }
}
