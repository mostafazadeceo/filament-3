<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AccessService;
use Filamat\IamSuite\Support\AccessSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class PermissionSimulator extends Page
{
    use AuthorizesIam;
    use InteractsWithForms;

    protected static ?string $permission = 'iam.view';

    protected static ?string $navigationLabel = 'شبیه‌ساز دسترسی';

    protected static ?string $title = 'شبیه‌ساز دسترسی';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-beaker';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    protected string $view = 'filamat-iam::pages.permission-simulator';

    public array $data = [];

    public ?array $result = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('tenant_id')
                    ->label('فضای کاری')
                    ->options(fn () => Tenant::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('user_id')
                    ->label('کاربر')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('permission_key')
                    ->label('مجوز')
                    ->searchable()
                    ->options(function (callable $get) {
                        $tenantId = $get('tenant_id');
                        $tenant = $tenantId ? Tenant::query()->find($tenantId) : null;

                        return AccessSettings::permissionOptions($tenant);
                    })
                    ->required(),
            ])
            ->statePath('data');
    }

    public function simulate(): void
    {
        $data = $this->form->getState();

        $tenant = Tenant::query()->find($data['tenant_id']);
        $user = (config('auth.providers.users.model'))::query()->find($data['user_id']);

        if (! $tenant || ! $user) {
            $this->result = null;

            return;
        }

        $this->result = app(AccessService::class)->explainPermission($user, $tenant, $data['permission_key']);
    }
}
