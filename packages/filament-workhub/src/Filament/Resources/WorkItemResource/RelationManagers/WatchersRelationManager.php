<?php

namespace Haida\FilamentWorkhub\Filament\Resources\WorkItemResource\RelationManagers;

use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WatchersRelationManager extends RelationManager
{
    protected static string $relationship = 'watchers';

    protected static ?string $title = 'دنبال‌کننده‌ها';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('user_id')
                    ->label('کاربر')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with('user'))
            ->defaultSort('created_at', 'desc');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['tenant_id'] = $this->getOwnerRecord()->tenant_id;

        return $data;
    }
}
