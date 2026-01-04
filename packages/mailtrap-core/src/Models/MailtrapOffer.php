<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class MailtrapOffer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'status',
        'description',
        'duration_days',
        'feature_keys',
        'limits',
        'price',
        'currency',
        'catalog_product_id',
        'metadata',
        'created_by_user_id',
        'updated_by_user_id',
    ];

    protected $casts = [
        'feature_keys' => 'array',
        'limits' => 'array',
        'price' => 'decimal:4',
        'metadata' => 'array',
    ];

    public function getTable(): string
    {
        return config('mailtrap-core.tables.offers', 'mailtrap_offers');
    }
}
