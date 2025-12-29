<?php

namespace Haida\FilamentRestaurantOps\Models;

use Haida\FilamentRestaurantOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantRecipe extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'restaurant_recipes';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'name',
        'code',
        'yield_quantity',
        'yield_uom_id',
        'waste_percent',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'yield_quantity' => 'decimal:4',
        'waste_percent' => 'decimal:2',
        'is_active' => 'bool',
        'metadata' => 'array',
    ];

    public function yieldUom(): BelongsTo
    {
        return $this->belongsTo(RestaurantUom::class, 'yield_uom_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(RestaurantRecipeLine::class, 'recipe_id');
    }
}
