<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantPurchaseRequest extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_purchase_requests';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'branch_id',
        'requested_by',
        'status',
        'needed_at',
        'notes',
    ];

    protected $casts = [
        'needed_at' => 'date',
    ];

    public function lines(): HasMany
    {
        return $this->hasMany(RestaurantPurchaseRequestLine::class, 'purchase_request_id');
    }
}
