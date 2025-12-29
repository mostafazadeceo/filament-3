<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vendor\FilamentAccountingIr\Models\Party;

class RestaurantSupplier extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'restaurant_suppliers';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'accounting_party_id',
        'name',
        'code',
        'status',
        'contact_name',
        'phone',
        'email',
        'address',
        'payment_terms',
        'rating',
        'metadata',
    ];

    protected $casts = [
        'rating' => 'int',
        'metadata' => 'array',
    ];

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(RestaurantPurchaseOrder::class, 'supplier_id');
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(RestaurantGoodsReceipt::class, 'supplier_id');
    }

    public function accountingParty(): BelongsTo
    {
        return $this->belongsTo(Party::class, 'accounting_party_id');
    }
}
