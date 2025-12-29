<?php

namespace Haida\FilamentRestaurantOps\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantMenuSaleLine extends Model
{
    use HasFactory;

    protected $table = 'restaurant_menu_sale_lines';

    protected $fillable = [
        'menu_sale_id',
        'menu_item_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:4',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuSale::class, 'menu_sale_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'menu_item_id');
    }
}
