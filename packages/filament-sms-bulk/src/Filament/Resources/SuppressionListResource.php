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
use Haida\SmsBulk\Filament\Resources\SuppressionListResource\Pages\CreateSuppression;
use Haida\SmsBulk\Filament\Resources\SuppressionListResource\Pages\EditSuppression;
use Haida\SmsBulk\Filament\Resources\SuppressionListResource\Pages\ListSuppressions;
use Haida\SmsBulk\Models\SmsBulkSuppressionList;

class SuppressionListResource extends Resource
{
    protected static ?string $model = SmsBulkSuppressionList::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-no-symbol';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.suppression');
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
                Select::make('source')->label(__('filament-sms-bulk::messages.fields.source'))->options([
                    'manual' => 'manual',
                    'keyword' => 'keyword',
                    'import' => 'import',
                    'bounce' => 'bounce',
                ])->default('manual')->required(),
                TextInput::make('reason')->label(__('filament-sms-bulk::messages.fields.reason')),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('msisdn')->label(__('filament-sms-bulk::messages.fields.msisdn'))->searchable(),
            TextColumn::make('source')->label(__('filament-sms-bulk::messages.fields.source'))->badge(),
            TextColumn::make('reason')->label(__('filament-sms-bulk::messages.fields.reason')),
            TextColumn::make('created_at')->label(__('filament-sms-bulk::messages.fields.created_at'))->jalaliDateTime(),
        ])->actions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppressions::route('/'),
            'create' => CreateSuppression::route('/create'),
            'edit' => EditSuppression::route('/{record}/edit'),
        ];
    }
}
