<?php

namespace Haida\FilamentRelograde\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RelogradeBrand extends Model
{
    protected $table = 'relograde_brands';

    protected $fillable = [
        'connection_id',
        'slug',
        'brand_name',
        'category',
        'redeem_type',
        'raw_json',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'raw_json' => 'array',
            'synced_at' => 'datetime',
        ];
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(RelogradeConnection::class, 'connection_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(RelogradeBrandOption::class, 'brand_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(RelogradeProduct::class, 'brand_slug', 'slug')
            ->where('connection_id', $this->connection_id);
    }
}
