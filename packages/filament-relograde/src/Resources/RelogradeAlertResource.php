<?php

namespace Haida\FilamentRelograde\Resources;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Haida\FilamentRelograde\Models\RelogradeAlert;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeAlertResource\Pages\ListRelogradeAlerts;
use Haida\FilamentRelograde\Resources\RelogradeAlertResource\Pages\ViewRelogradeAlert;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Support\RelogradeNotifier;

class RelogradeAlertResource extends Resource
{
    protected static ?string $model = RelogradeAlert::class;

    protected static ?string $modelLabel = 'هشدار رلوگرید';

    protected static ?string $pluralModelLabel = 'هشدارهای رلوگرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationLabel = 'هشدارها';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 8;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::alertType($state)),
                TextColumn::make('severity')
                    ->label('شدت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::severity($state)),
                TextColumn::make('currency')->label('ارز')->badge()->toggleable(),
                TextColumn::make('current_amount')->label('مقدار فعلی')->numeric(4),
                TextColumn::make('threshold')->label('آستانه')->numeric(4),
                TextColumn::make('resolved_at')->label('زمان رفع')->jalaliDateTime()->sortable(),
                TextColumn::make('created_at')->label('زمان ایجاد')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('severity')
                    ->label('شدت')
                    ->options([
                        'warning' => 'هشدار',
                        'critical' => 'بحرانی',
                    ]),
                SelectFilter::make('type')
                    ->label('نوع')
                    ->options(fn () => RelogradeAlert::query()
                        ->whereNotNull('type')
                        ->distinct()
                        ->pluck('type')
                        ->mapWithKeys(fn ($value) => [$value => RelogradeLabels::alertType($value)])
                        ->toArray()),
                TernaryFilter::make('resolved')
                    ->label('وضعیت رفع')
                    ->trueLabel('رفع شده')
                    ->falseLabel('فعال')
                    ->placeholder('همه')
                    ->queries(
                        true: fn ($query) => $query->whereNotNull('resolved_at'),
                        false: fn ($query) => $query->whereNull('resolved_at'),
                        blank: fn ($query) => $query,
                    ),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
                Action::make('resolve')
                    ->label('رفع')
                    ->icon('heroicon-o-check')
                    ->visible(fn (RelogradeAlert $record) => $record->resolved_at === null)
                    ->action(function (RelogradeAlert $record) {
                        $record->update(['resolved_at' => now()]);
                        RelogradeNotifier::success('هشدار رفع شد.');
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeAlerts::route('/'),
            'view' => ViewRelogradeAlert::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('هشدار')
                    ->schema([
                        TextEntry::make('type')
                            ->label('نوع')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::alertType($state)),
                        TextEntry::make('severity')
                            ->label('شدت')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::severity($state)),
                        TextEntry::make('currency')->label('ارز'),
                        TextEntry::make('current_amount')->label('مقدار فعلی'),
                        TextEntry::make('threshold')->label('آستانه'),
                        TextEntry::make('message')->label('پیام')->columnSpanFull(),
                        TextEntry::make('resolved_at')->label('زمان رفع')->jalaliDateTime(),
                        TextEntry::make('created_at')->label('زمان ایجاد')->jalaliDateTime(),
                    ])
                    ->columns(2),
            ]);
    }
}
