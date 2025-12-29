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

class TimeEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'timeEntries';

    protected static ?string $title = 'ثبت زمان';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('minutes')->label('دقیقه')->numeric()->required(),
                DateTimePicker::make('started_at')->label('شروع')->nullable(),
                DateTimePicker::make('ended_at')->label('پایان')->nullable(),
                Textarea::make('note')->label('یادداشت')->rows(3)->nullable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('minutes')->label('دقیقه'),
                TextColumn::make('started_at')->label('شروع'),
                TextColumn::make('ended_at')->label('پایان'),
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
