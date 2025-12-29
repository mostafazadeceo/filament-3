<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DecisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'decisions';

    protected static ?string $title = 'تصمیم‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')->label('عنوان')->required()->maxLength(255),
                Textarea::make('body')->label('جزئیات')->rows(4)->nullable(),
                DateTimePicker::make('decided_at')->label('زمان تصمیم')->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->limit(50),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('decided_at')->label('زمان'),
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
