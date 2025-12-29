<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PettyCashExpenseAttachment extends Model
{
    use HasFactory;
    use UsesTenant;

    protected $table = 'petty_cash_expense_attachments';

    protected $fillable = [
        'tenant_id',
        'company_id',
        'expense_id',
        'uploaded_by',
        'path',
        'original_name',
        'mime_type',
        'size',
        'metadata',
    ];

    protected $casts = [
        'size' => 'int',
        'metadata' => 'array',
    ];

    public function expense(): BelongsTo
    {
        return $this->belongsTo(PettyCashExpense::class, 'expense_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }
}
