<?php

namespace Haida\FilamentWorkhub\Models;

use App\Models\User;
use Haida\FilamentWorkhub\Database\Factories\WorkItemFactory;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkItem extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'workhub_work_items';

    protected $fillable = [
        'tenant_id',
        'project_id',
        'work_type_id',
        'workflow_id',
        'status_id',
        'number',
        'key',
        'title',
        'description',
        'priority',
        'reporter_id',
        'assignee_id',
        'due_date',
        'started_at',
        'completed_at',
        'estimate_minutes',
        'sort_order',
        'meta',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimate_minutes' => 'int',
        'sort_order' => 'int',
        'meta' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class, 'work_type_id');
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'work_item_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class, 'work_item_id');
    }

    public function watchers(): HasMany
    {
        return $this->hasMany(Watcher::class, 'work_item_id');
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'workhub_label_work_item', 'work_item_id', 'label_id')
            ->withPivot('tenant_id')
            ->withTimestamps();
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class, 'work_item_id');
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(Decision::class, 'work_item_id');
    }

    public function auditEvents(): HasMany
    {
        return $this->hasMany(AuditEvent::class, 'work_item_id');
    }

    public function customFieldValues(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'work_item_id');
    }

    public function aiSummaries(): HasMany
    {
        return $this->hasMany(AiSummary::class, 'work_item_id');
    }

    public function aiFieldRuns(): HasMany
    {
        return $this->hasMany(AiFieldRun::class, 'work_item_id');
    }

    public function links(): MorphMany
    {
        return $this->morphMany(EntityReference::class, 'source', 'source_type', 'source_id');
    }

    protected static function newFactory(): WorkItemFactory
    {
        return WorkItemFactory::new();
    }
}
