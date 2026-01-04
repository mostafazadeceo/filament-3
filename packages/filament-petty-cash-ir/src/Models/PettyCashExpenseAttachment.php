<?php

namespace Haida\FilamentPettyCashIr\Models;

use Haida\FilamentPettyCashIr\Models\Concerns\UsesTenant;
use Haida\FilamentPettyCashIr\Services\PettyCashControlService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

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
        'content_hash',
        'metadata',
    ];

    protected $casts = [
        'size' => 'int',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (PettyCashExpenseAttachment $attachment): void {
            if (! $attachment->content_hash) {
                $attachment->content_hash = $attachment->computeContentHash();
            }
        });

        static::created(function (PettyCashExpenseAttachment $attachment): void {
            if (! config('filament-petty-cash-ir.attachments.duplicate_detection', true)) {
                return;
            }

            app(PettyCashControlService::class)->checkDuplicateReceipt($attachment);
        });
    }

    public function expense(): BelongsTo
    {
        return $this->belongsTo(PettyCashExpense::class, 'expense_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'uploaded_by');
    }

    protected function computeContentHash(): ?string
    {
        $path = $this->path;
        if (! $path) {
            return null;
        }

        $disk = config('filament-petty-cash-ir.attachments.disk', 'public');
        $storage = Storage::disk($disk);
        if (! $storage->exists($path)) {
            return null;
        }

        $fullPath = $storage->path($path);
        if (! is_file($fullPath)) {
            return null;
        }

        return hash_file('sha256', $fullPath) ?: null;
    }
}
