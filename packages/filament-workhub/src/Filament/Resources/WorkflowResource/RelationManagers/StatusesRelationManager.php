<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkflowResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StatusesRelationManager extends RelationManager
{
    protected static string $relationship = 'statuses';

    protected static ?string $title = 'وضعیت‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('کلید')
                    ->required()
                    ->maxLength(255),
                Select::make('category')
                    ->label('دسته')
                    ->options([
                        'todo' => 'برای انجام',
                        'in_progress' => 'در حال انجام',
                        'done' => 'انجام شده',
                    ])
                    ->required(),
                TextInput::make('color')
                    ->label('رنگ')
                    ->maxLength(20)
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_default')->label('پیش‌فرض')->default(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('slug')->label('کلید'),
                TextColumn::make('category')
                    ->label('دسته')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'todo' => 'برای انجام',
                        'in_progress' => 'در حال انجام',
                        'done' => 'انجام شده',
                        default => $state,
                    }),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean(),
                TextColumn::make('sort_order')->label('ترتیب'),
            ])
            ->defaultSort('sort_order');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $this->getOwnerRecord()->tenant_id;
        $data['workflow_id'] = $this->getOwnerRecord()->getKey();

        return $data;
    }
}
