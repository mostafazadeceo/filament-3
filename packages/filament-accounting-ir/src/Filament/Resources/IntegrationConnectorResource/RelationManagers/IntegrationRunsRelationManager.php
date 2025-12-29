<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IntegrationRunsRelationManager extends RelationManager
{
    protected static string $relationship = 'runs';

    protected static ?string $title = 'اجراها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status')
                    ->label('وضعیت')
                    ->maxLength(64),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('started_at')->label('شروع')->jalaliDateTime(),
                TextColumn::make('finished_at')->label('پایان')->jalaliDateTime(),
            ])
            ->defaultSort('started_at', 'desc');
    }
}
