<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Database\Factories\WorkTypeFactory;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkType extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_work_types';

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'sort_order' => 'int',
    ];

    public function workItems(): HasMany
    {
        return $this->hasMany(WorkItem::class, 'work_type_id');
    }

    protected static function newFactory(): WorkTypeFactory
    {
        return WorkTypeFactory::new();
    }
}
