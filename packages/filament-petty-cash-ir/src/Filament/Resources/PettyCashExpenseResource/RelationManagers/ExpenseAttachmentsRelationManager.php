<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources\PettyCashExpenseResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpenseAttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Hidden::make('tenant_id')
                    ->default(fn () => $this->getOwnerRecord()->tenant_id)
                    ->dehydrated(true),
                Hidden::make('company_id')
                    ->default(fn () => $this->getOwnerRecord()->company_id)
                    ->dehydrated(true),
                Hidden::make('uploaded_by')
                    ->default(fn () => auth()->id())
                    ->dehydrated(true),
                FileUpload::make('path')
                    ->label('فایل')
                    ->disk('public')
                    ->directory('petty-cash/expenses')
                    ->preserveFilenames()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('path')->label('مسیر فایل')->limit(40),
                TextColumn::make('created_at')->label('تاریخ')->jalaliDateTime(),
            ]);
    }
}
