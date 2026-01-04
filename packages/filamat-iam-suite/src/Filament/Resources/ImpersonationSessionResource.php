<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\ImpersonationSessionResource\Pages\ListImpersonationSessions;
use Filamat\IamSuite\Models\ImpersonationSession;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ImpersonationSessionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = ImpersonationSession::class;

    protected static ?string $navigationLabel = 'امپرسونیشن';

    protected static ?string $pluralModelLabel = 'امپرسونیشن';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-eye';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('impersonator.name')->label('کاربر امپرسونیشن'),
                TextColumn::make('impersonated.name')->label('کاربر هدف'),
                TextColumn::make('restricted')
                    ->label('حالت مشاهده')
                    ->formatStateUsing(fn (bool $state) => $state ? 'بله' : 'خیر'),
                TextColumn::make('started_at')->label('شروع'),
                TextColumn::make('expires_at')->label('انقضا'),
                TextColumn::make('ended_at')->label('پایان'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListImpersonationSessions::route('/'),
        ];
    }
}
