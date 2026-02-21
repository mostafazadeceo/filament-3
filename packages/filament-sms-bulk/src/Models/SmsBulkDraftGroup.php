<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SmsBulkDraftGroup extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_draft_groups';

    protected $fillable = [
        'tenant_id',
        'name_translations',
        'description_translations',
        'meta',
    ];

    protected $casts = [
        'name_translations' => 'array',
        'description_translations' => 'array',
        'meta' => 'array',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(SmsBulkDraftMessage::class, 'draft_group_id');
    }
}
