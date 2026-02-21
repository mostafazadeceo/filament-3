<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsBulkSuppressionList extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_suppression_lists';

    protected $fillable = [
        'tenant_id',
        'msisdn',
        'reason',
        'source',
        'created_by',
    ];
}
