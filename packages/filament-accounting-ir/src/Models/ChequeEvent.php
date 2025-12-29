<?php

namespace Vendor\FilamentAccountingIr\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChequeEvent extends Model
{
    use HasFactory;

    protected $table = 'accounting_ir_cheque_events';

    protected $fillable = [
        'cheque_id',
        'event_date',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'event_date' => 'date',
        'metadata' => 'array',
    ];

    public function cheque(): BelongsTo
    {
        return $this->belongsTo(Cheque::class, 'cheque_id');
    }
}
