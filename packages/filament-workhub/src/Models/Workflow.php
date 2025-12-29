<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Database\Factories\WorkflowFactory;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workflow extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'workhub_workflows';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'is_default',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_default' => 'bool',
    ];

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class, 'workflow_id');
    }

    public function transitions(): HasMany
    {
        return $this->hasMany(Transition::class, 'workflow_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'workflow_id');
    }

    public function workItems(): HasMany
    {
        return $this->hasMany(WorkItem::class, 'workflow_id');
    }

    protected static function newFactory(): WorkflowFactory
    {
        return WorkflowFactory::new();
    }
}
