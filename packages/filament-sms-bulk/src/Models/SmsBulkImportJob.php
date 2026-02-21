<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBulkImportJob extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_import_jobs';

    protected $fillable = [
        'tenant_id',
        'phonebook_id',
        'type',
        'status',
        'input_path',
        'output_path',
        'total_rows',
        'success_rows',
        'failed_rows',
        'meta',
        'finished_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'finished_at' => 'datetime',
    ];

    public function phonebook(): BelongsTo
    {
        return $this->belongsTo(SmsBulkPhonebook::class, 'phonebook_id');
    }
}
