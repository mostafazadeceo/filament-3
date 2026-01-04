<?php

namespace Haida\CommerceCatalog\Filament\Resources\CatalogProductResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogMediaRelationManager extends RelationManager
{
    protected static string $relationship = 'media';

    protected static ?string $recordTitleAttribute = 'url';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'image' => 'تصویر',
                        'video' => 'ویدیو',
                        'file' => 'فایل',
                    ])
                    ->default('image')
                    ->required(),
                TextInput::make('url')
                    ->label('آدرس')
                    ->required()
                    ->maxLength(2048),
                TextInput::make('alt')
                    ->label('متن جایگزین')
                    ->maxLength(255),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_primary')
                    ->label('اصلی')
                    ->default(false),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('url')->label('آدرس')->limit(40),
                TextColumn::make('sort_order')->label('ترتیب'),
                IconColumn::make('is_primary')->label('اصلی')->boolean(),
            ]);
    }
}
