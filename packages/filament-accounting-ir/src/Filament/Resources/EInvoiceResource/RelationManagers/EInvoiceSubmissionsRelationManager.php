<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EInvoiceSubmissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'submissions';

    protected static ?string $title = 'ارسال‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('status')->label('وضعیت')->maxLength(64),
                TextInput::make('correlation_id')->label('شناسه پیگیری')->maxLength(255),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('correlation_id')->label('شناسه پیگیری'),
                TextColumn::make('created_at')->label('زمان')->jalaliDateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
