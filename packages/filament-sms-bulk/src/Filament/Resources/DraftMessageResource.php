<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SmsBulk\Filament\Resources\DraftMessageResource\Pages\CreateDraftMessage;
use Haida\SmsBulk\Filament\Resources\DraftMessageResource\Pages\EditDraftMessage;
use Haida\SmsBulk\Filament\Resources\DraftMessageResource\Pages\ListDraftMessages;
use Haida\SmsBulk\Models\SmsBulkDraftGroup;
use Haida\SmsBulk\Models\SmsBulkDraftMessage;

class DraftMessageResource extends Resource
{
    protected static ?string $model = SmsBulkDraftMessage::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.drafts');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.draft.view', 'sms-bulk.draft.manage']);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('draft_group_id')
                    ->label(__('filament-sms-bulk::messages.fields.group'))
                    ->options(fn () => SmsBulkDraftGroup::query()->pluck('name_translations->fa', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('language')->label(__('filament-sms-bulk::messages.fields.language'))->options(['fa' => 'fa', 'en' => 'en', 'ar' => 'ar'])->default('fa'),
                TextInput::make('title_translations.fa')->label(__('filament-sms-bulk::messages.fields.name')),
                Textarea::make('body_translations.fa')->label(__('filament-sms-bulk::messages.fields.message'))->required()->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('draftGroup.name_translations.fa')->label(__('filament-sms-bulk::messages.fields.group')),
            TextColumn::make('title_translations.fa')->label(__('filament-sms-bulk::messages.fields.name')),
            TextColumn::make('language')->label(__('filament-sms-bulk::messages.fields.language'))->badge(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDraftMessages::route('/'),
            'create' => CreateDraftMessage::route('/create'),
            'edit' => EditDraftMessage::route('/{record}/edit'),
        ];
    }
}
