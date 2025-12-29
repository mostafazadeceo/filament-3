<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IntegrationMappingsRelationManager extends RelationManager
{
    protected static string $relationship = 'mappings';

    protected static ?string $title = 'مپینگ‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('entity')
                    ->label('موجودیت')
                    ->required()
                    ->maxLength(128),
                Textarea::make('mapping')
                    ->label('مپینگ')
                    ->helperText('JSON')
                    ->afterStateHydrated(function (Textarea $component, $state): void {
                        if (is_array($state)) {
                            $component->state(json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                        }
                    })
                    ->dehydrateStateUsing(fn ($state) => $state ? json_decode((string) $state, true) : [])
                    ->required(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('entity')->label('موجودیت')->searchable()->sortable(),
                TextColumn::make('created_at')->label('ایجاد')->jalaliDateTime(),
            ]);
    }
}
