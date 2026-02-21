<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBulkDraftMessage extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_draft_messages';

    protected $fillable = [
        'tenant_id',
        'draft_group_id',
        'title_translations',
        'body_translations',
        'language',
        'meta',
    ];

    protected $casts = [
        'title_translations' => 'array',
        'body_translations' => 'array',
        'meta' => 'array',
    ];

    public function draftGroup(): BelongsTo
    {
        return $this->belongsTo(SmsBulkDraftGroup::class, 'draft_group_id');
    }
}
