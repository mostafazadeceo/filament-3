<?php

namespace Haida\FilamentAiCore\Models;

use App\Models\User;
use Haida\FilamentAiCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFeedback extends Model
{
    use UsesTenant;

    public const UPDATED_AT = null;

    protected $table = 'ai_feedback';

    protected $fillable = [
        'tenant_id',
        'actor_id',
        'module',
        'action_type',
        'target_type',
        'target_id',
        'rating',
        'note',
        'created_at',
    ];

    protected $casts = [
        'rating' => 'int',
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
