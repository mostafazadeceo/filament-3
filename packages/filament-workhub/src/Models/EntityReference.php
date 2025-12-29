<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EntityReference extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_entity_references';

    protected $fillable = [
        'tenant_id',
        'source_type',
        'source_id',
        'target_type',
        'target_id',
        'relation_type',
    ];

    public function source(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'source_type', 'source_id');
    }

    public function target(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'target_type', 'target_id');
    }
}
