<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFieldValue extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_custom_field_values';

    protected $fillable = [
        'tenant_id',
        'field_id',
        'work_item_id',
        'project_id',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'field_id');
    }

    public function workItem(): BelongsTo
    {
        return $this->belongsTo(WorkItem::class, 'work_item_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
