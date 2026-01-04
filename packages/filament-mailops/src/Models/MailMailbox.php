<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Models;

use Haida\FilamentMailOps\Models\Concerns\UsesTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MailMailbox extends Model
{
    use UsesTenant;

    protected $fillable = [
        'tenant_id',
        'domain_id',
        'local_part',
        'email',
        'display_name',
        'password',
        'status',
        'quota_bytes',
        'settings',
        'sync_status',
        'last_error',
        'mailu_synced_at',
        'comment',
    ];

    protected $casts = [
        'settings' => 'array',
        'mailu_synced_at' => 'datetime',
        'quota_bytes' => 'integer',
        'password' => 'encrypted',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if (! $model->domain_id || ! $model->local_part) {
                return;
            }

            if ($model->isDirty(['local_part', 'domain_id']) || ! $model->email) {
                $domain = $model->relationLoaded('domain') ? $model->domain : MailDomain::query()->find($model->domain_id);
                if ($domain) {
                    $model->email = $model->local_part.'@'.$domain->name;
                }
            }
        });
    }

    public function domain(): BelongsTo
    {
        return $this->belongsTo(MailDomain::class, 'domain_id');
    }

    public function outboundMessages(): HasMany
    {
        return $this->hasMany(MailOutboundMessage::class, 'mailbox_id');
    }

    public function inboundMessages(): HasMany
    {
        return $this->hasMany(MailInboundMessage::class, 'mailbox_id');
    }

    public function getTable(): string
    {
        return config('filament-mailops.tables.mailboxes', 'mailops_mailboxes');
    }
}
