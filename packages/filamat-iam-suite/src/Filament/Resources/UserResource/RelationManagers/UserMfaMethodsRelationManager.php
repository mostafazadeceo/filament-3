<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filamat\IamSuite\Models\MfaMethod;
use Filamat\IamSuite\Services\MfaService;
use Filamat\IamSuite\Services\ProtectedActionService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserMfaMethodsRelationManager extends RelationManager
{
    protected static string $relationship = 'mfaMethods';

    protected static ?string $title = 'احراز هویت دومرحله‌ای';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('enabled_at')->label('فعال‌سازی'),
                TextColumn::make('last_used_at')->label('آخرین استفاده'),
                TextColumn::make('revoked_at')->label('غیرفعال'),
            ])
            ->actions([
                Action::make('reset')
                    ->label('ریست')
                    ->color('danger')
                    ->visible(fn (MfaMethod $record) => $record->revoked_at === null && IamAuthorization::allows('mfa.reset'))
                    ->form([
                        Textarea::make('reason')->label('دلیل')->required(),
                        TextInput::make('password')->label('رمز عبور')->password()->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('totp')->label('کد MFA')->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('backup_code')->label('کد پشتیبان')->dehydrated(fn ($state) => filled($state)),
                    ])
                    ->action(function (MfaMethod $record, array $data) {
                        $user = $record->user;
                        if (! $user) {
                            return;
                        }

                        $tenant = $record->tenant;
                        $requiresStepUp = in_array('iam.mfa.reset', (array) config('filamat-iam.protected_actions.require_mfa_actions', []), true);
                        if ($requiresStepUp) {
                            $protected = app(ProtectedActionService::class);
                            if (! empty($data['totp'])) {
                                $token = $protected->issueWithTotp($user, 'iam.mfa.reset', $data['totp'], $tenant);
                                $protected->requireToken($user, 'iam.mfa.reset', $tenant, $token);
                            } elseif (! empty($data['backup_code'])) {
                                $token = $protected->issueWithBackupCode($user, 'iam.mfa.reset', $data['backup_code'], $tenant);
                                $protected->requireToken($user, 'iam.mfa.reset', $tenant, $token);
                            } elseif (! empty($data['password'])) {
                                $token = $protected->issueWithPassword($user, 'iam.mfa.reset', $data['password'], $tenant);
                                $protected->requireToken($user, 'iam.mfa.reset', $tenant, $token);
                            } else {
                                throw new \RuntimeException('تایید هویت مجدد لازم است.');
                            }
                        }

                        app(MfaService::class)->reset($user, $tenant, auth()->user(), $data['reason'] ?? null);
                    }),
            ]);
    }
}
