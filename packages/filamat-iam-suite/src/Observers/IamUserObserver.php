<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Observers;

use Filamat\IamSuite\Services\Automation\IamEventFactory;
use Filamat\IamSuite\Services\Automation\IamEventPublisher;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class IamUserObserver
{
    public function __construct(
        protected IamEventFactory $eventFactory,
        protected IamEventPublisher $publisher,
    ) {}

    public function created(Model $user): void
    {
        $actor = auth()->user();
        if ($user instanceof Authenticatable) {
            $event = $this->eventFactory->fromUserCreated($user, TenantContext::getTenant(), $actor);
            $this->publisher->publish($event);
        }
    }

    public function updated(Model $user): void
    {
        if (! $user instanceof Authenticatable) {
            return;
        }

        $changes = $this->sanitizeChanges($user->getChanges(), $user->getOriginal());
        if ($changes === []) {
            return;
        }

        $actor = auth()->user();
        $event = $this->eventFactory->fromUserUpdated($user, $changes, TenantContext::getTenant(), $actor);
        $this->publisher->publish($event);
    }

    public function deleted(Model $user): void
    {
        $actor = auth()->user();
        if ($user instanceof Authenticatable) {
            $event = $this->eventFactory->fromUserDeleted($user, TenantContext::getTenant(), $actor);
            $this->publisher->publish($event);
        }
    }

    /**
     * @param  array<string, mixed>  $changes
     * @param  array<string, mixed>  $original
     * @return array<string, array<string, mixed>>
     */
    protected function sanitizeChanges(array $changes, array $original): array
    {
        $blocked = [
            'password',
            'remember_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
            'api_token',
        ];

        $allowed = [
            'name',
            'email',
            'phone',
            'status',
            'suspended_at',
            'deleted_at',
            'last_login_at',
            'last_logout_at',
        ];

        $filtered = Arr::only($changes, $allowed);
        foreach ($blocked as $blockedKey) {
            unset($filtered[$blockedKey]);
        }

        $result = [];
        foreach ($filtered as $key => $to) {
            $from = $original[$key] ?? null;
            if (in_array($key, ['email', 'phone'], true)) {
                $from = $this->maskValue($from);
                $to = $this->maskValue($to);
            }

            $result[$key] = [
                'from' => $from,
                'to' => $to,
            ];
        }

        return $result;
    }

    protected function maskValue(mixed $value): string
    {
        $value = (string) ($value ?? '');
        if ($value === '') {
            return '***';
        }

        return substr($value, 0, 1).'***';
    }
}
