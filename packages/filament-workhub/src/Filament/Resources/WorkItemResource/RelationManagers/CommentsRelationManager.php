<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $title = 'دیدگاه‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Textarea::make('body')
                    ->label('متن')
                    ->required()
                    ->rows(4),
                Toggle::make('is_internal')
                    ->label('داخلی')
                    ->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('body')->label('متن')->limit(80)->wrap(),
                IconColumn::make('is_internal')->label('داخلی')->boolean(),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('created_at', 'desc');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $this->getOwnerRecord()->tenant_id;
        $data['user_id'] = auth()->id();

        return $data;
    }
}
