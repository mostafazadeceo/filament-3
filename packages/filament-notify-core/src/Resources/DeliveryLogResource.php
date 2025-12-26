<?php

namespace Haida\FilamentNotify\Core\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Haida\FilamentNotify\Core\Models\DeliveryLog;
use Haida\FilamentNotify\Core\Resources\DeliveryLogResource\Pages\ListDeliveryLogs;
use Illuminate\Database\Eloquent\Builder;

class DeliveryLogResource extends Resource
{
    protected static ?string $model = DeliveryLog::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'گزارش ارسال';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاع‌رسانی';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trigger_key')->label('تریگر')->searchable(),
                TextColumn::make('channel')->label('کانال')->badge(),
                TextColumn::make('recipient')->label('گیرنده')->searchable(),
                BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->colors([
                        'success' => 'sent',
                        'danger' => 'failed',
                        'warning' => 'skipped',
                    ]),
                TextColumn::make('created_at')->label('زمان')->jalaliDateTime(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveryLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $panelId = \Filament\Facades\Filament::getCurrentPanel()?->getId();

        if (! $panelId) {
            return $query;
        }

        return $query->where('panel_id', $panelId);
    }
}
