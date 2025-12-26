<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\AuditLogResource\Pages\ListAuditLogs;
use Filamat\IamSuite\Models\AuditLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'audit';

    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationLabel = 'گزارش ممیزی';

    protected static ?string $pluralModelLabel = 'گزارش ممیزی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'گزارش‌ها';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('action')->label('عملیات'),
                TextColumn::make('actor.name')->label('کاربر'),
                TextColumn::make('subject_type')->label('نوع هدف')->limit(30),
                TextColumn::make('subject_id')->label('شناسه هدف'),
                TextColumn::make('ip')->label('آی‌پی'),
                TextColumn::make('created_at')->label('زمان'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
        ];
    }
}
