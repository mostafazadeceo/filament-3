<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Database\Factories\TransitionFactory;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transition extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_transitions';

    protected $fillable = [
        'tenant_id',
        'workflow_id',
        'name',
        'from_status_id',
        'to_status_id',
        'is_active',
        'sort_order',
        'validators',
        'post_actions',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'sort_order' => 'int',
        'validators' => 'array',
        'post_actions' => 'array',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function fromStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'from_status_id');
    }

    public function toStatus(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'to_status_id');
    }

    protected static function newFactory(): TransitionFactory
    {
        return TransitionFactory::new();
    }
}
