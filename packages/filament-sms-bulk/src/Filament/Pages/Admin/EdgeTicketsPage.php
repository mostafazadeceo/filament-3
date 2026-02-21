<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Filament\Pages\Admin;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Pages\Page;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Services\ProviderClientFactory;

class EdgeTicketsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-ellipsis';

    protected static string|\UnitEnum|null $navigationGroup = 'filament-sms-bulk::messages.nav.group';

    protected static ?string $slug = 'sms-bulk/admin/edge-tickets';

    protected string $view = 'filament-sms-bulk::filament.pages.edge-tickets';

    /** @var array<int, array<string, mixed>> */
    public array $items = [];

    public static function getNavigationLabel(): string
    {
        return __('filament-sms-bulk::messages.nav.admin_tickets');
    }

    public static function canAccess(): bool
    {
        return IamAuthorization::allowsAny(['sms-bulk.ticket.manage', 'sms-bulk.reseller.manage']);
    }

    public function mount(ProviderClientFactory $factory): void
    {
        $connection = SmsBulkProviderConnection::query()->latest('id')->first();
        if (! $connection) {
            return;
        }

        try {
            $response = $factory->make($connection)->ticketList();
            $this->items = array_values((array) ($response['data']['items'] ?? $response['data'] ?? []));
        } catch (\Throwable) {
            $this->items = [];
        }
    }
}
