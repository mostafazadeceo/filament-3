<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepreciationEntry extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_depreciation_entries';

    protected $fillable = [
        'fixed_asset_id',
        'journal_entry_id',
        'posted_at',
        'amount',
        'metadata',
    ];

    protected $casts = [
        'posted_at' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(FixedAsset::class, 'fixed_asset_id');
    }

    public function journalEntry(): BelongsTo
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }
}
