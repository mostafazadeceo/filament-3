<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RestaurantUom extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'restaurant_uoms';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'symbol',
        'is_base',
    ];

    protected $casts = [
        'is_base' => 'bool',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(RestaurantItem::class, 'base_uom_id');
    }
}
