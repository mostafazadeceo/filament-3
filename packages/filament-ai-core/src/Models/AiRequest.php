<?php

namespace Haida\FilamentAiCore\Models;

use App\Models\User;
use Haida\FilamentAiCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRequest extends Model
{
    use UsesTenant;

    public const UPDATED_AT = null;

    protected $table = 'ai_requests';

    protected $fillable = [
        'tenant_id',
        'actor_id',
        'module',
        'action_type',
        'input_hash',
        'output_hash',
        'status',
        'latency_ms',
        'created_at',
    ];

    protected $casts = [
        'latency_ms' => 'int',
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
