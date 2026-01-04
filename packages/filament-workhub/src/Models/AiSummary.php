<?php

namespace Haida\FilamentWorkhub\Models;

use App\Models\User;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSummary extends Model
{
    use UsesTenant;

    public const UPDATED_AT = null;

    protected $table = 'workhub_ai_summaries';

    protected $fillable = [
        'tenant_id',
        'work_item_id',
        'created_by',
        'type',
        'provider',
        'prompt_version',
        'summary_json',
        'ttl_expires_at',
        'created_at',
    ];

    protected $casts = [
        'summary_json' => 'array',
        'ttl_expires_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class, 'work_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
