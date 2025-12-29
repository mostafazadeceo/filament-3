<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Database\Factories\StatusFactory;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Status extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_statuses';

    protected $fillable = [
        'tenant_id',
        'workflow_id',
        'name',
        'slug',
        'category',
        'color',
        'sort_order',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'bool',
        'sort_order' => 'int',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    protected static function newFactory(): StatusFactory
    {
        return StatusFactory::new();
    }
}
