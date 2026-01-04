<?php

namespace Haida\FilamentPos\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosRegister;
use Haida\FilamentPos\Models\PosStore;
use Haida\FilamentPos\Models\PosSyncCursor;
use Illuminate\Support\Carbon;

class PosSyncService
{
    /**
     * @return array<string, mixed>
     */
    public function snapshot(?PosDevice $device = null): array
    {
        $cursor = now()->toIso8601String();

        $stores = PosStore::query()->get()->toArray();
        $registers = PosRegister::query()->get()->toArray();

        $catalog = $this->catalogSnapshot();

        $this->touchCursor($device, $cursor);

        return [
            'cursor' => $cursor,
            'stores' => $stores,
            'registers' => $registers,
            'catalog' => $catalog,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function delta(string $sinceCursor, ?PosDevice $device = null): array
    {
        $cursorTime = Carbon::parse($sinceCursor);
        $cursor = now()->toIso8601String();

        $stores = PosStore::query()->where('updated_at', '>', $cursorTime)->get()->toArray();
        $registers = PosRegister::query()->where('updated_at', '>', $cursorTime)->get()->toArray();
        $catalog = $this->catalogSnapshot($cursorTime);

        $this->touchCursor($device, $cursor);

        return [
            'cursor' => $cursor,
            'stores' => $stores,
            'registers' => $registers,
            'catalog' => $catalog,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function catalogSnapshot(?Carbon $since = null): array
    {
        $tenantId = TenantContext::getTenantId();
        $data = [
            'products' => [],
            'variants' => [],
            'prices' => [],
            'inventory' => [],
        ];

        if (class_exists(\Haida\FilamentCommerceCore\Models\CommerceProduct::class)) {
            $query = \Haida\FilamentCommerceCore\Models\CommerceProduct::query()
                ->where('tenant_id', $tenantId)
                ->where('status', 'active');
            if ($since) {
                $query->where('updated_at', '>', $since);
            }
            $data['products'] = $query->get()->toArray();
        }

        if (class_exists(\Haida\FilamentCommerceCore\Models\CommerceVariant::class)) {
            $query = \Haida\FilamentCommerceCore\Models\CommerceVariant::query()
                ->where('tenant_id', $tenantId)
                ->where('status', 'active');
            if ($since) {
                $query->where('updated_at', '>', $since);
            }
            $data['variants'] = $query->get()->toArray();
        }

        if (class_exists(\Haida\FilamentCommerceCore\Models\CommercePrice::class)) {
            $query = \Haida\FilamentCommerceCore\Models\CommercePrice::query()
                ->where('tenant_id', $tenantId);
            if ($since) {
                $query->where('updated_at', '>', $since);
            }
            $data['prices'] = $query->get()->toArray();
        }

        if (class_exists(\Haida\FilamentCommerceCore\Models\CommerceInventoryItem::class)) {
            $query = \Haida\FilamentCommerceCore\Models\CommerceInventoryItem::query()
                ->where('tenant_id', $tenantId);
            if ($since) {
                $query->where('updated_at', '>', $since);
            }
            $data['inventory'] = $query->get()->toArray();
        }

        return $data;
    }

    protected function touchCursor(?PosDevice $device, string $cursor): void
    {
        if (! $device) {
            return;
        }

        PosSyncCursor::query()->updateOrCreate([
            'tenant_id' => $device->tenant_id,
            'device_id' => $device->getKey(),
        ], [
            'cursor' => $cursor,
            'last_synced_at' => now(),
            'metadata' => ['source' => 'api'],
        ]);
    }
}
