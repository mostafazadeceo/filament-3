<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Models;

use Haida\SmsBulk\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsBulkPhonebookOption extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'sms_bulk_phonebook_options';

    protected $fillable = [
        'tenant_id',
        'phonebook_id',
        'name',
        'type',
        'is_required',
        'meta',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'meta' => 'array',
    ];

    public function phonebook(): BelongsTo
    {
        return $this->belongsTo(SmsBulkPhonebook::class, 'phonebook_id');
    }
}
