<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EsimGoProduct extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'catalog_product_id',
        'catalog_variant_id',
        'bundle_name',
        'provider_product_id',
        'description',
        'groups',
        'countries',
        'countries_meta',
        'region',
        'allowances',
        'price',
        'currency',
        'data_amount_mb',
        'duration_days',
        'speed',
        'autostart',
        'unlimited',
        'roaming_enabled',
        'billing_type',
        'status',
    ];

    protected $casts = [
        'groups' => 'array',
        'countries' => 'array',
        'countries_meta' => 'array',
        'region' => 'array',
        'allowances' => 'array',
        'speed' => 'array',
        'roaming_enabled' => 'array',
        'autostart' => 'bool',
        'unlimited' => 'bool',
        'price' => 'decimal:4',
        'data_amount_mb' => 'integer',
        'duration_days' => 'integer',
    ];

    public function getTable(): string
    {
        return config('providers-esim-go-core.tables.products', 'esim_go_products');
    }

    public function catalogProduct(): BelongsTo
    {
        return $this->belongsTo(CatalogProduct::class, 'catalog_product_id');
    }

    public function catalogVariant(): BelongsTo
    {
        return $this->belongsTo(CatalogVariant::class, 'catalog_variant_id');
    }
}
