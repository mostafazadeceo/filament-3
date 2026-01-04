<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\UserResource;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\InviteUserService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ListUsers extends ListRecordsWithCreate
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            Action::make('stopImpersonation')
                ->label('توقف امپرسونیشن')
                ->color('warning')
                ->visible(fn () => app(ImpersonationService::class)->isImpersonating())
                ->action(function () {
                    app(ImpersonationService::class)->stop();

                    return redirect()->to('/');
                }),
            Action::make('invite')
                ->label('دعوت کاربر')
                ->visible(fn () => IamAuthorization::allowsAny(['iam.manage', 'user.invite']))
                ->form([
                    TextInput::make('name')->label('نام')->required(),
                    TextInput::make('email')->label('ایمیل')->email()->required(),
                    Textarea::make('reason')->label('دلیل')->required(),
                    DateTimePicker::make('expires_at')->label('انقضا')->nullable(),
                ])
                ->action(function (array $data) {
                    $tenant = TenantContext::getTenant();
                    if (! $tenant) {
                        return;
                    }

                    app(InviteUserService::class)->invite(
                        $tenant,
                        (string) $data['email'],
                        (string) $data['name'],
                        [],
                        [],
                        auth()->user(),
                        (string) ($data['reason'] ?? ''),
                        $data['expires_at'] ?? null
                    );
                }),
        ];
    }
}
