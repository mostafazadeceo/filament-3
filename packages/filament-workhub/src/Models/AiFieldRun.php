<?php

namespace Haida\FilamentWorkhub\Models;

use App\Models\User;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiFieldRun extends Model
{
    use UsesTenant;

    public const UPDATED_AT = null;

    protected $table = 'workhub_ai_field_runs';

    protected $fillable = [
        'tenant_id',
        'field_id',
        'work_item_id',
        'output_json',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'output_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'field_id');
    }

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class, 'work_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
