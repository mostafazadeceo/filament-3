<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Services\SessionService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserSessionsRelationManager extends RelationManager
{
    protected static string $relationship = 'iamSessions';

    protected static ?string $title = 'نشست‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
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
                        app(SessionService::class)->revoke($record, auth()->user(), $data['reason'] ?? null);
                    }),
            ]);
    }
}
