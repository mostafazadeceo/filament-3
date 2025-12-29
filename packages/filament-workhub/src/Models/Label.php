<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Label extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_labels';

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'color',
    ];

    public function workItems(): BelongsToMany
    {
        return $this->belongsToMany(WorkItem::class, 'workhub_label_work_item', 'label_id', 'work_item_id')
            ->withTimestamps();
    }
}
