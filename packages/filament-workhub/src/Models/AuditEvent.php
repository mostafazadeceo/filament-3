<?php

namespace Haida\FilamentWorkhub\Models;

use App\Models\User;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditEvent extends Model
{
    use HasFactory;
    use UsesTenant;

    public $timestamps = false;

    protected $table = 'workhub_audit_events';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'work_item_id',
        'actor_id',
        'event',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class, 'work_item_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
