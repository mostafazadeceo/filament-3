<?php

namespace Haida\FilamentAiCore\Models;

use Haida\FilamentAiCore\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;

class AiPolicy extends Model
{
    use UsesTenant;

    protected $table = 'ai_policies';

    protected $fillable = [
        'tenant_id',
        'enabled',
        'provider',
        'redaction_policy',
        'retention_days',
        'consent_required_meetings',
        'allow_store_transcripts',
    ];

    protected $casts = [
        'enabled' => 'bool',
        'redaction_policy' => 'array',
        'retention_days' => 'int',
        'consent_required_meetings' => 'bool',
        'allow_store_transcripts' => 'bool',
    ];
}
