<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Support;

use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentCryptoGateway\Services\ReconcileService;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CryptoGatewayScheduler
{
    public static function reconcileInvoices(): void
    {
        if (! Schema::hasTable('tenants')) {
            return;
        }

        if (! Schema::hasTable(config('filament-crypto-gateway.tables.invoices', 'crypto_invoices'))) {
            return;
        }

        $tenantIds = Tenant::query()->pluck('id')->all();

        foreach ($tenantIds as $tenantId) {
            try {
                app(ReconcileService::class)->run((int) $tenantId, 'invoices');
            } catch (Throwable) {
                // Keep scheduler resilient.
            }
        }
    }

    public static function reconcileDaily(): void
    {
        if (! Schema::hasTable('tenants')) {
            return;
        }

        $tenantIds = Tenant::query()->pluck('id')->all();

        foreach ($tenantIds as $tenantId) {
            try {
                app(ReconcileService::class)->run((int) $tenantId, 'daily');
            } catch (Throwable) {
                // Keep scheduler resilient.
            }
        }
    }
}
