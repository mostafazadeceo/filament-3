<?php

namespace Haida\FilamentNotify\Core\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentNotify\Core\Models\Trigger;
use Haida\FilamentNotify\Core\Resources\TriggerResource\Pages\ListTriggers;
use Illuminate\Database\Eloquent\Builder;

class TriggerResource extends Resource
{
    protected static ?string $model = Trigger::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'تریگرها';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاع‌رسانی';

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('کلید')
                    ->searchable(),
                TextColumn::make('label')
                    ->label('عنوان')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->badge(),
                TextColumn::make('updated_at')
                    ->label('آخرین بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTriggers::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forPanel();
    }
}
