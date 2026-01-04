<?php

namespace Haida\FilamentWorkhub\Models;

use Haida\FilamentWorkhub\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomField extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'workhub_custom_fields';

    protected $fillable = [
        'tenant_id',
        'scope',
        'name',
        'key',
        'type',
        'settings',
        'is_required',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_required' => 'bool',
        'is_active' => 'bool',
        'sort_order' => 'int',
    ];

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class, 'field_id');
    }

    public function aiRuns(): HasMany
    {
        return $this->hasMany(AiFieldRun::class, 'field_id');
    }
}
