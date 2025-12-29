<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class JournalApproval extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_journal_approvals';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'journal_entry_id',
        'user_id',
        'action',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
