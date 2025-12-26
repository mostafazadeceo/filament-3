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
use Filament\Tables\Table;
use Haida\FilamentRelograde\Jobs\ProcessWebhookEventJob;
use Haida\FilamentRelograde\Models\RelogradeWebhookEvent;
use Haida\FilamentRelograde\Resources\RelogradeWebhookEventResource\Pages\ListRelogradeWebhookEvents;
use Haida\FilamentRelograde\Resources\RelogradeWebhookEventResource\Pages\ViewRelogradeWebhookEvent;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Support\RelogradeNotifier;

class RelogradeWebhookEventResource extends Resource
{
    protected static ?string $model = RelogradeWebhookEvent::class;

    protected static ?string $modelLabel = 'رویداد وب‌هوک';

    protected static ?string $pluralModelLabel = 'رویدادهای وب‌هوک';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'رویدادهای وب‌هوک';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 6;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('event')
                    ->label('رویداد')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::webhookEvent($state)),
                TextColumn::make('state')
                    ->label('محیط')
                    ->badge()
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::environment($state)),
                TextColumn::make('trx')->label('شناسه تراکنش')->searchable(),
                TextColumn::make('reference')->label('مرجع')->toggleable(),
                TextColumn::make('processing_status')
                    ->label('وضعیت پردازش')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::processingStatus($state)),
                TextColumn::make('received_ip')->label('آی‌پی دریافتی')->toggleable(),
                TextColumn::make('created_at')->label('زمان دریافت')->jalaliDateTime()->sortable(),
                TextColumn::make('processed_at')->label('زمان پردازش')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => \Haida\FilamentRelograde\Models\RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('processing_status')
                    ->label('وضعیت پردازش')
                    ->options([
                        'pending' => 'در انتظار',
                        'processed' => 'پردازش شده',
                        'failed' => 'ناموفق',
                    ]),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
                Action::make('reprocess')
                    ->label('بازپردازش')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (RelogradeWebhookEvent $record) {
                        ProcessWebhookEventJob::dispatch($record->getKey());
                        RelogradeNotifier::success('رویداد وب‌هوک در صف قرار گرفت.');
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeWebhookEvents::route('/'),
            'view' => ViewRelogradeWebhookEvent::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('رویداد وب‌هوک')
                    ->schema([
                        TextEntry::make('event')
                            ->label('رویداد')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::webhookEvent($state)),
                        TextEntry::make('state')
                            ->label('محیط')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::environment($state)),
                        TextEntry::make('api_key_description')->label('توضیح کلید ای‌پی‌آی'),
                        TextEntry::make('trx')->label('شناسه تراکنش'),
                        TextEntry::make('reference')->label('مرجع'),
                        TextEntry::make('received_ip')->label('آی‌پی دریافتی'),
                        TextEntry::make('processing_status')
                            ->label('وضعیت پردازش')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::processingStatus($state)),
                        TextEntry::make('processed_at')->label('زمان پردازش')->jalaliDateTime(),
                        TextEntry::make('error_message')->label('خطا'),
                    ])
                    ->columns(2),
                Section::make('بار داده')
                    ->schema([
                        TextEntry::make('payload')
                            ->formatStateUsing(function ($state) {
                                return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
