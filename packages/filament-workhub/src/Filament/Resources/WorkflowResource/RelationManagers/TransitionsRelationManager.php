<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkflowResource\RelationManagers;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentWorkhub\Models\Status;

class TransitionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transitions';

    protected static ?string $title = 'انتقال‌ها';

    public function form(Schema $schema): Schema
    {
        $workflowId = $this->getOwnerRecord()->getKey();

        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Select::make('from_status_id')
                    ->label('از وضعیت')
                    ->options(fn () => Status::query()->where('workflow_id', $workflowId)->pluck('name', 'id')->toArray())
                    ->required(),
                Select::make('to_status_id')
                    ->label('به وضعیت')
                    ->options(fn () => Status::query()->where('workflow_id', $workflowId)->pluck('name', 'id')->toArray())
                    ->required(),
                Toggle::make('is_active')->label('فعال')->default(true),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                KeyValue::make('validators')->label('قوانین اعتبارسنجی')->nullable(),
                KeyValue::make('post_actions')->label('اقدامات پس از انتقال')->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('fromStatus.name')->label('از'),
                TextColumn::make('toStatus.name')->label('به'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
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
