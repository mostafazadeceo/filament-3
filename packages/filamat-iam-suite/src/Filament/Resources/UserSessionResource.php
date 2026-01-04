<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\UserSessionResource\Pages\ListUserSessions;
use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserSessionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'session';

    protected static ?string $model = UserSession::class;

    protected static ?string $navigationLabel = 'نشست‌ها';

    protected static ?string $pluralModelLabel = 'نشست‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

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
                TextColumn::make('user.name')->label('کاربر')->searchable(),
                TextColumn::make('ip')->label('IP'),
                TextColumn::make('user_agent')->label('دستگاه')->limit(40),
                TextColumn::make('last_activity_at')->label('آخرین فعالیت'),
                TextColumn::make('revoked_at')->label('ابطال'),
            ])
            ->actions([
                Action::make('revoke')
                    ->label('ابطال')
                    ->color('danger')
                    ->visible(fn (UserSession $record) => $record->revoked_at === null && IamAuthorization::allows('session.revoke'))
                    ->form([
                        Textarea::make('reason')->label('دلیل')->required(),
                    ])
                    ->action(function (UserSession $record, array $data) {
                        app(\Filamat\IamSuite\Services\SessionService::class)
                            ->revoke($record, auth()->user(), $data['reason'] ?? null);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserSessions::route('/'),
        ];
    }
}
