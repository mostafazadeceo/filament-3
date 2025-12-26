<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Widgets;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Filament\Resources\ApiKeyResource;
use Filamat\IamSuite\Filament\Resources\GroupResource;
use Filamat\IamSuite\Filament\Resources\NotificationResource;
use Filamat\IamSuite\Filament\Resources\OrganizationResource;
use Filamat\IamSuite\Filament\Resources\PermissionTemplateResource;
use Filamat\IamSuite\Filament\Resources\RoleResource;
use Filamat\IamSuite\Filament\Resources\SubscriptionPlanResource;
use Filamat\IamSuite\Filament\Resources\SubscriptionResource;
use Filamat\IamSuite\Filament\Resources\TenantResource;
use Filamat\IamSuite\Filament\Resources\UserResource;
use Filamat\IamSuite\Filament\Resources\WalletResource;
use Filamat\IamSuite\Filament\Resources\WebhookResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    use AuthorizesIam;

    protected static ?string $permission = 'iam.view';

    protected string $view = 'filamat-iam::widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    /**
     * @return array{actions: array<int, array{label: string, description: string, icon: string, url: string}>}
     */
    protected function getViewData(): array
    {
        $actions = [];

        if (TenantContext::shouldBypass()) {
            $actions[] = $this->resourceAction(
                OrganizationResource::class,
                'ایجاد سازمان',
                'ثبت سازمان جدید',
                'heroicon-o-building-office-2',
                'create'
            );
            $actions[] = $this->resourceAction(
                TenantResource::class,
                'ایجاد فضای کاری',
                'ساخت پنل جدید',
                'heroicon-o-rectangle-group',
                'create'
            );
        }

        $actions[] = $this->resourceAction(
            UserResource::class,
            'ایجاد کاربر',
            'دعوت یا ساخت حساب کاربری',
            'heroicon-o-user-plus',
            'create'
        );
        $actions[] = $this->resourceAction(
            RoleResource::class,
            'ایجاد نقش',
            'تعریف نقش‌های دسترسی',
            'heroicon-o-shield-check',
            'create'
        );
        $actions[] = $this->resourceAction(
            GroupResource::class,
            'ایجاد گروه',
            'سازماندهی تیم‌ها و گروه‌ها',
            'heroicon-o-user-group',
            'create'
        );
        $actions[] = $this->resourceAction(
            PermissionTemplateResource::class,
            'قالب دسترسی',
            'ساخت یا مدیریت قالب‌ها',
            'heroicon-o-clipboard-document-check',
            'create'
        );
        $actions[] = $this->resourceAction(
            WalletResource::class,
            'ایجاد کیف پول',
            'مدیریت موجودی کاربران',
            'heroicon-o-credit-card',
            'create'
        );
        $actions[] = $this->resourceAction(
            SubscriptionPlanResource::class,
            'ایجاد پلن',
            'تعریف پلن‌های اشتراک',
            'heroicon-o-tag',
            'create'
        );
        $actions[] = $this->resourceAction(
            SubscriptionResource::class,
            'ایجاد اشتراک',
            'ثبت اشتراک جدید',
            'heroicon-o-bolt',
            'create'
        );
        $actions[] = $this->resourceAction(
            NotificationResource::class,
            'ارسال اعلان',
            'ارسال پیام به کاربران',
            'heroicon-o-bell',
            'create'
        );
        $actions[] = $this->resourceAction(
            WebhookResource::class,
            'ثبت وبهوک',
            'افزودن گیرنده رویدادها',
            'heroicon-o-paper-airplane',
            'create'
        );
        $actions[] = $this->resourceAction(
            ApiKeyResource::class,
            'ایجاد کلید API',
            'مدیریت توکن‌های دسترسی',
            'heroicon-o-key',
            'create'
        );

        return [
            'actions' => array_values(array_filter($actions)),
        ];
    }

    /**
     * @param  class-string  $resource
     * @return array{label: string, description: string, icon: string, url: string}|null
     */
    private function resourceAction(string $resource, string $label, string $description, string $icon, string $page): ?array
    {
        if (! $resource::hasPage($page)) {
            return null;
        }

        if ($page === 'create' && ! $resource::canCreate()) {
            return null;
        }

        if ($page === 'index' && ! $resource::canViewAny()) {
            return null;
        }

        $url = $resource::getUrl($page);
        if (! $url) {
            return null;
        }

        return [
            'label' => $label,
            'description' => $description,
            'icon' => $icon,
            'url' => $url,
        ];
    }
}
