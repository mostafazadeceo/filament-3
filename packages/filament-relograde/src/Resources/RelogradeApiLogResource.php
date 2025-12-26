<?php

namespace Haida\FilamentRelograde\Resources;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentRelograde\Models\RelogradeApiLog;
use Haida\FilamentRelograde\Resources\RelogradeApiLogResource\Pages\ListRelogradeApiLogs;
use Haida\FilamentRelograde\Resources\RelogradeApiLogResource\Pages\ViewRelogradeApiLog;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeLabels;

class RelogradeApiLogResource extends Resource
{
    protected static ?string $model = RelogradeApiLog::class;

    protected static ?string $modelLabel = 'گزارش ای‌پی‌آی';

    protected static ?string $pluralModelLabel = 'گزارش‌های ای‌پی‌آی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'گزارش‌های ای‌پی‌آی';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 7;

    public static function shouldRegisterNavigation(): bool
    {
        return RelogradeAuthorization::can('logs_view');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('method')->label('روش')->badge(),
                TextColumn::make('endpoint_name')
                    ->label('عملیات')
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::endpointName($state)),
                TextColumn::make('response_status')->label('کد پاسخ')->badge(),
                TextColumn::make('duration_ms')->label('مدت (میلی‌ثانیه)')->numeric(),
                TextColumn::make('created_at')->label('زمان ثبت')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => \Haida\FilamentRelograde\Models\RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('response_status')
                    ->label('کد پاسخ')
                    ->options(fn () => RelogradeApiLog::query()->whereNotNull('response_status')->distinct()->pluck('response_status', 'response_status')->toArray()),
                SelectFilter::make('endpoint_name')
                    ->label('عملیات')
                    ->options(fn () => RelogradeApiLog::query()
                        ->whereNotNull('endpoint_name')
                        ->distinct()
                        ->pluck('endpoint_name')
                        ->mapWithKeys(fn ($value) => [$value => RelogradeLabels::endpointName($value)])
                        ->toArray())
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeApiLogs::route('/'),
            'view' => ViewRelogradeApiLog::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('درخواست')
                    ->schema([
                        TextEntry::make('method')->label('روش'),
                        TextEntry::make('url')->label('نشانی'),
                        TextEntry::make('endpoint_name')
                            ->label('عملیات')
                            ->formatStateUsing(fn ($state) => RelogradeLabels::endpointName($state)),
                        TextEntry::make('request_headers')->label('هدرها')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->columnSpanFull(),
                        TextEntry::make('request_body')->label('بدنه')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('پاسخ')
                    ->schema([
                        TextEntry::make('response_status')->label('کد پاسخ'),
                        TextEntry::make('duration_ms')->label('مدت (میلی‌ثانیه)'),
                        TextEntry::make('response_body')->label('بدنه پاسخ')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->columnSpanFull(),
                        TextEntry::make('error')->label('خطا'),
                    ])
                    ->columns(2),
            ]);
    }
}
