<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EInvoiceStatusLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'statusLogs';

    protected static ?string $title = 'لاگ وضعیت';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status')->label('وضعیت')->maxLength(64),
                TextInput::make('message')->label('پیام')->maxLength(255),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('message')->label('پیام'),
                TextColumn::make('created_at')->label('زمان')->jalaliDateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
