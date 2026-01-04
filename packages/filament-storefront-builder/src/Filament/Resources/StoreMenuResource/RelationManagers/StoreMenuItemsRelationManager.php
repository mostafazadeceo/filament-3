<?php

namespace Haida\FilamentStorefrontBuilder\Filament\Resources\StoreMenuResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentStorefrontBuilder\Models\StoreMenuItem;
use Haida\FilamentStorefrontBuilder\Models\StorePage;

class StoreMenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('parent_id')
                    ->label('والد')
                    ->options(fn () => StoreMenuItem::query()->pluck('label', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('page_id')
                    ->label('صفحه')
                    ->options(fn () => StorePage::query()->pluck('title', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('label')
                    ->label('برچسب')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->label('نشانی')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('برچسب')
                    ->searchable(),
                TextColumn::make('url')
                    ->label('نشانی'),
                TextColumn::make('sort_order')
                    ->label('ترتیب'),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $data['tenant_id'] ?? $this->getOwnerRecord()->tenant_id;

        return $data;
    }
}
