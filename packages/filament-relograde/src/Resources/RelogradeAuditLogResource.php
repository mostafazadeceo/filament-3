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
use Haida\FilamentRelograde\Models\RelogradeAuditLog;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Resources\RelogradeAuditLogResource\Pages\ListRelogradeAuditLogs;
use Haida\FilamentRelograde\Resources\RelogradeAuditLogResource\Pages\ViewRelogradeAuditLog;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeLabels;

class RelogradeAuditLogResource extends Resource
{
    protected static ?string $model = RelogradeAuditLog::class;

    protected static ?string $modelLabel = 'لاگ ممیزی';

    protected static ?string $pluralModelLabel = 'لاگ‌های ممیزی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'گزارش‌های ممیزی';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 9;

    public static function shouldRegisterNavigation(): bool
    {
        return RelogradeAuthorization::can('logs_view');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('action')
                    ->label('اقدام')
                    ->badge()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::auditAction($state)),
                TextColumn::make('entity_type')
                    ->label('نوع موجودیت')
                    ->toggleable()
                    ->formatStateUsing(fn ($state) => RelogradeLabels::entityType($state)),
                TextColumn::make('entity_id')->label('شناسه موجودیت')->toggleable(),
                TextColumn::make('user_id')->label('کاربر')->toggleable(),
                TextColumn::make('ip_address')->label('آی‌پی')->toggleable(),
                TextColumn::make('created_at')->label('زمان ثبت')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('action')
                    ->label('اقدام')
                    ->options(fn () => RelogradeAuditLog::query()
                        ->whereNotNull('action')
                        ->distinct()
                        ->pluck('action')
                        ->mapWithKeys(fn ($value) => [$value => RelogradeLabels::auditAction($value)])
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
            'index' => ListRelogradeAuditLogs::route('/'),
            'view' => ViewRelogradeAuditLog::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('گزارش ممیزی')
                    ->schema([
                        TextEntry::make('action')
                            ->label('اقدام')
                            ->badge()
                            ->formatStateUsing(fn ($state) => RelogradeLabels::auditAction($state)),
                        TextEntry::make('entity_type')
                            ->label('نوع موجودیت')
                            ->formatStateUsing(fn ($state) => RelogradeLabels::entityType($state)),
                        TextEntry::make('entity_id')->label('شناسه موجودیت'),
                        TextEntry::make('user_id')->label('کاربر'),
                        TextEntry::make('ip_address')->label('آی‌پی'),
                        TextEntry::make('user_agent')->label('عامل کاربر'),
                        TextEntry::make('created_at')->label('زمان ثبت')->jalaliDateTime(),
                    ])
                    ->columns(2),
                Section::make('بار داده')
                    ->schema([
                        TextEntry::make('payload')
                            ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
