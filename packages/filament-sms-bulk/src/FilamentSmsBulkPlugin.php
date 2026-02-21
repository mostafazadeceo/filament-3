<?php

declare(strict_types=1);

namespace Haida\SmsBulk;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\SmsBulk\Filament\Pages\Admin\EdgeNumbersPage;
use Haida\SmsBulk\Filament\Pages\Admin\EdgePackagesPage;
use Haida\SmsBulk\Filament\Pages\Admin\EdgeTicketsPage;
use Haida\SmsBulk\Filament\Pages\Admin\EdgeUsersPage;
use Haida\SmsBulk\Filament\Resources\CampaignRecipientResource;
use Haida\SmsBulk\Filament\Resources\CampaignResource;
use Haida\SmsBulk\Filament\Resources\ConsentRegistryResource;
use Haida\SmsBulk\Filament\Resources\ContactResource;
use Haida\SmsBulk\Filament\Resources\DraftMessageResource;
use Haida\SmsBulk\Filament\Resources\PatternTemplateResource;
use Haida\SmsBulk\Filament\Resources\PhonebookResource;
use Haida\SmsBulk\Filament\Resources\ProviderConnectionResource;
use Haida\SmsBulk\Filament\Resources\QuietHoursProfileResource;
use Haida\SmsBulk\Filament\Resources\QuotaPolicyResource;
use Haida\SmsBulk\Filament\Resources\SenderIdentityResource;
use Haida\SmsBulk\Filament\Resources\SuppressionListResource;
use Haida\SmsBulk\Filament\Widgets\SmsBulkOverviewWidget;
use Haida\SmsBulk\Filament\Widgets\SmsBulkQueueHealthWidget;

class FilamentSmsBulkPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'sms-bulk';
    }

    public function register(Panel $panel): void
    {
        $tenantResources = [
            ProviderConnectionResource::class,
            SenderIdentityResource::class,
            PhonebookResource::class,
            ContactResource::class,
            PatternTemplateResource::class,
            DraftMessageResource::class,
            CampaignResource::class,
            CampaignRecipientResource::class,
            SuppressionListResource::class,
            ConsentRegistryResource::class,
            QuietHoursProfileResource::class,
            QuotaPolicyResource::class,
        ];

        if ($panel->getId() === 'admin') {
            $panel
                ->resources($tenantResources)
                ->pages([
                    EdgeUsersPage::class,
                    EdgePackagesPage::class,
                    EdgeNumbersPage::class,
                    EdgeTicketsPage::class,
                ])
                ->widgets([
                    SmsBulkOverviewWidget::class,
                    SmsBulkQueueHealthWidget::class,
                ]);

            return;
        }

        if ($panel->getId() === 'tenant') {
            $panel
                ->resources($tenantResources)
                ->widgets([
                    SmsBulkOverviewWidget::class,
                    SmsBulkQueueHealthWidget::class,
                ]);
        }
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
