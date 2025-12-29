<?php

namespace Haida\FilamentRestaurantOps\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantRecipeLine extends Model
{
    use HasFactory;

    protected $table = 'restaurant_recipe_lines';

    protected $fillable = [
        'recipe_id',
        'item_id',
        'uom_id',
        'quantity',
        'waste_percent',
        'is_optional',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'waste_percent' => 'decimal:2',
        'is_optional' => 'bool',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(RestaurantRecipe::class, 'recipe_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(RestaurantItem::class, 'item_id');
    }

    public function uom(): BelongsTo
    {
        return $this->belongsTo(RestaurantUom::class, 'uom_id');
    }
}
