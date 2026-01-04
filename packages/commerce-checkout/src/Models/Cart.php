<?php

namespace Haida\CommerceCheckout\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Cart extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'public_id',
        'tenant_id',
        'site_id',
        'user_id',
        'status',
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'total',
        'meta',
        'expires_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'expires_at' => 'datetime',
        'subtotal' => 'decimal:4',
        'discount_total' => 'decimal:4',
        'tax_total' => 'decimal:4',
        'shipping_total' => 'decimal:4',
        'total' => 'decimal:4',
    ];

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function getTable(): string
    {
        return config('commerce-checkout.tables.carts', 'commerce_checkout_carts');
    }

    protected static function booted(): void
    {
        static::creating(function (Cart $cart): void {
            if (! $cart->public_id) {
                $cart->public_id = (string) Str::uuid();
            }
        });
    }
}
