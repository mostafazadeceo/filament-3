<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Pages\Admin;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Pages\Page;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Services\ProviderClientFactory;

class EdgeNumbersPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-phone-mobile';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    protected static ?string $slug = 'sms-bulk/admin/edge-numbers';

    protected string $view = 'filament-sms-bulk::filament.pages.edge-numbers';

    /** @var array<int, array<string, mixed>> */
    public array $items = [];

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.admin_numbers');
    }

    public static function canAccess(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.reseller.view', 'sms-bulk.reseller.manage']);
    }

    public function mount(ProviderClientFactory $factory): void
    {
        $connection = SmsBulkProviderConnection::query()->latest('id')->first();
        if (! $connection) {
            return;
        }

        try {
            $response = $factory->make($connection)->numberPoolList();
            $this->items = array_values((array) ($response['data']['items'] ?? $response['data'] ?? []));
        } catch (\Throwable) {
            $this->items = [];
        }
    }
}
