<?php

namespace Haida\FilamentWorkhub\Models;

use App\Models\User;
use Haida\FilamentWorkhub\Database\Factories\ProjectFactory;
use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;
    use UsesTenant;

    protected $table = 'workhub_projects';

    protected $fillable = [
        'tenant_id',
        'workflow_id',
        'key',
        'name',
        'description',
        'status',
        'lead_user_id',
        'start_date',
        'due_date',
        'allowed_link_types',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'allowed_link_types' => 'array',
    ];

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_user_id');
    }

    public function workItems(): HasMany
    {
        return $this->hasMany(WorkItem::class, 'project_id');
    }

    public function allowsLinkType(string $type): bool
    {
        $allowed = $this->allowed_link_types ?? [];
        if ($allowed === [] || $allowed === null) {
            return true;
        }

        return in_array($type, $allowed, true);
    }

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }
}
