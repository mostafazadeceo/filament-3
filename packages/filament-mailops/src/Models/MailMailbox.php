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
            $domain = null;

            if ($model->domain_id) {
                $domain = $model->relationLoaded('domain') ? $model->domain : MailDomain::query()->find($model->domain_id);
            }

            if ($domain && ($model->isDirty(['local_part', 'domain_id']) || ! $model->email) && filled($model->local_part)) {
                $model->email = $model->local_part.'@'.$domain->name;
            }

            $model->settings = self::normalizeConnectionSettings(
                is_array($model->settings) ? $model->settings : [],
                $domain?->name
            );
        });
    }

    public static function normalizeConnectionSettings(array $settings, ?string $domainName = null): array
    {
        $settings = self::cleanSettings($settings);
        $mailHost = self::resolveDefaultMailHost($domainName);

        $defaults = [
            'enable_imap' => (bool) config('filament-mailops.mailbox_defaults.enable_imap', true),
            'enable_pop' => (bool) config('filament-mailops.mailbox_defaults.enable_pop', true),
            'smtp_host' => (string) (config('filament-mailops.smtp.host') ?: $mailHost),
            'smtp_port' => (int) config('filament-mailops.smtp.port', 587),
            'smtp_encryption' => (string) config('filament-mailops.smtp.encryption', 'tls'),
            'smtp_verify_tls' => (bool) config('filament-mailops.smtp.verify_tls', true),
            'smtp_ehlo_domain' => (string) (config('filament-mailops.smtp.ehlo_domain') ?: ($domainName ?: '')),
            'imap_host' => (string) (config('filament-mailops.imap.host') ?: $mailHost),
            'imap_port' => (int) config('filament-mailops.imap.port', 993),
            'imap_encryption' => (string) config('filament-mailops.imap.encryption', 'ssl'),
            'imap_verify_tls' => (bool) config('filament-mailops.imap.verify_tls', true),
            'pop_host' => (string) (config('filament-mailops.pop.host') ?: $mailHost),
            'pop_port' => (int) config('filament-mailops.pop.port', 995),
            'pop_encryption' => (string) config('filament-mailops.pop.encryption', 'ssl'),
            'pop_verify_tls' => (bool) config('filament-mailops.pop.verify_tls', true),
        ];

        foreach ($defaults as $key => $defaultValue) {
            if (! array_key_exists($key, $settings) || self::isBlankSetting($settings[$key])) {
                $settings[$key] = $defaultValue;
            }
        }

        foreach (['smtp_port', 'imap_port', 'pop_port'] as $portKey) {
            if (array_key_exists($portKey, $settings) && ! self::isBlankSetting($settings[$portKey])) {
                $settings[$portKey] = (int) $settings[$portKey];
            }
        }

        foreach (['enable_imap', 'enable_pop', 'smtp_verify_tls', 'imap_verify_tls', 'pop_verify_tls', 'allow_spoofing', 'forward_enabled', 'forward_keep', 'reply_enabled'] as $boolKey) {
            if (array_key_exists($boolKey, $settings)) {
                $settings[$boolKey] = (bool) $settings[$boolKey];
            }
        }

        $settings['smtp_host'] = trim((string) $settings['smtp_host']);
        $settings['imap_host'] = trim((string) $settings['imap_host']);
        $settings['pop_host'] = trim((string) $settings['pop_host']);
        $settings['smtp_encryption'] = trim((string) $settings['smtp_encryption']);
        $settings['imap_encryption'] = trim((string) $settings['imap_encryption']);
        $settings['pop_encryption'] = trim((string) $settings['pop_encryption']);

        if (self::isBlankSetting($settings['smtp_ehlo_domain'] ?? null)) {
            $settings['smtp_ehlo_domain'] = $domainName ?: null;
        }

        return $settings;
    }

    protected static function cleanSettings(array $settings): array
    {
        $clean = [];

        foreach ($settings as $key => $value) {
            if (! is_string($key) || trim($key) === '') {
                continue;
            }

            if (is_string($value)) {
                $trimmed = trim($value);
                $clean[$key] = $trimmed === '' ? null : $trimmed;

                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }

    protected static function resolveDefaultMailHost(?string $domainName): string
    {
        $configured = [
            config('filament-mailops.smtp.host'),
            config('filament-mailops.imap.host'),
            config('filament-mailops.pop.host'),
        ];

        foreach ($configured as $host) {
            $host = is_string($host) ? trim($host) : '';
            if ($host !== '') {
                return $host;
            }
        }

        $domainName = is_string($domainName) ? trim($domainName) : '';
        if ($domainName !== '') {
            return 'mail.'.$domainName;
        }

        return 'mail.abrak.org';
    }

    protected static function isBlankSetting(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        return false;
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
