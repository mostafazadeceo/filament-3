<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoAuditLogResource\Pages\ListCryptoAuditLogs;
use Haida\FilamentCryptoCore\Models\CryptoAuditEvent;

class CryptoAuditLogResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.audit';

    protected static ?string $model = CryptoAuditEvent::class;

    protected static ?string $modelLabel = 'رویداد مالی';

    protected static ?string $pluralModelLabel = 'رویدادهای مالی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('event_type')
                    ->label('رویداد')
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('نوع هدف')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('subject_id')
                    ->label('شناسه هدف')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('description')
                    ->label('توضیح')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->label('زمان')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCryptoAuditLogs::route('/'),
        ];
    }
}
