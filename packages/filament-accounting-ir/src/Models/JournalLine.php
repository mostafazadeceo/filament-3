<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Vendor\FilamentAccountingIr\Models\Concerns\UsesTenant;

class JournalLine extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'accounting_ir_journal_lines';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'journal_entry_id',
        'account_id',
        'description',
        'debit',
        'credit',
        'currency',
        'amount',
        'exchange_rate',
        'dimensions',
        'metadata',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'dimensions' => 'array',
        'metadata' => 'array',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartAccount::class, 'account_id');
    }
}
