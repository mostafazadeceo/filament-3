<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRule extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_automation_rules';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'name',
        'is_active',
        'trigger_type',
        'trigger_config',
        'conditions',
        'actions',
        'last_ran_at',
    ];

    protected $casts = [
        'is_active' => 'bool',
        'trigger_config' => 'array',
        'conditions' => 'array',
        'actions' => 'array',
        'last_ran_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
