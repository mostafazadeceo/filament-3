<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantMenuItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'restaurant_menu_items';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'recipe_id',
        'name',
        'code',
        'category',
        'price',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(RestaurantRecipe::class, 'recipe_id');
    }
}
