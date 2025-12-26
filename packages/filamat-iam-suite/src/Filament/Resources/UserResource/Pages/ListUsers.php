<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\Pages;

use Filamat\IamSuite\Events\UserInvited;
use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Filament\Resources\UserResource;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\NotificationService;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Spatie\Permission\Models\Role;

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
                ])
                ->action(function (array $data) {
                    $userModel = config('auth.providers.users.model');
                    $user = $userModel::query()->firstOrCreate(
                        ['email' => $data['email']],
                        ['name' => $data['name'], 'password' => bcrypt(str()->random(12))]
                    );

                    $tenant = TenantContext::getTenant();
                    if ($tenant && method_exists($user, 'tenants')) {
                        $user->tenants()->syncWithoutDetaching([
                            $tenant->getKey() => [
                                'role' => 'member',
                                'status' => 'invited',
                                'joined_at' => now(),
                            ],
                        ]);
                    }

                    if ($tenant) {
                        $defaultPermissions = AccessSettings::personDefaultPermissions($tenant);
                        foreach ($defaultPermissions as $permissionKey) {
                            PermissionOverride::query()->updateOrCreate([
                                'tenant_id' => $tenant->getKey(),
                                'user_id' => $user->getAuthIdentifier(),
                                'permission_key' => $permissionKey,
                            ], [
                                'effect' => 'allow',
                            ]);
                        }

                        $defaultRoles = AccessSettings::personDefaultRoles($tenant);
                        foreach ($defaultRoles as $roleName) {
                            $role = Role::query()
                                ->where('tenant_id', $tenant->getKey())
                                ->where('name', $roleName)
                                ->with('permissions')
                                ->first();
                            if (! $role) {
                                continue;
                            }

                            foreach ($role->permissions as $permission) {
                                PermissionOverride::query()->updateOrCreate([
                                    'tenant_id' => $tenant->getKey(),
                                    'user_id' => $user->getAuthIdentifier(),
                                    'permission_key' => $permission->name,
                                ], [
                                    'effect' => 'allow',
                                ]);
                            }
                        }
                    }

                    event(new UserInvited($user, $tenant));

                    app(NotificationService::class)->sendNotification($user, 'user.invited', [
                        'message' => 'دعوت‌نامه ارسال شد.',
                    ], $tenant);
                }),
        ];
    }
}
