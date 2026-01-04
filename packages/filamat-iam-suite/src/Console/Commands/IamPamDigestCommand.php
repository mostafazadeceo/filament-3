<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Console\Commands;

use Filamat\IamSuite\Models\PrivilegeActivation;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class IamPamDigestCommand extends Command
{
    protected $signature = 'iam:pam:digest';

    protected $description = 'Send weekly PAM activation digest notifications.';

    public function handle(NotificationService $notificationService): int
    {
        if (! (bool) config('filamat-iam.pam.digest.enabled', true)) {
            $this->info('PAM digest disabled.');

            return self::SUCCESS;
        }

        $daysAhead = (int) config('filamat-iam.pam.digest.days_ahead', 7);
        $cutoff = now()->addDays($daysAhead);

        $activeCounts = PrivilegeActivation::query()
            ->where('status', 'active')
            ->selectRaw('tenant_id, count(*) as total')
            ->groupBy('tenant_id')
            ->pluck('total', 'tenant_id');

        $expiring = PrivilegeActivation::query()
            ->with(['user:id,name,email', 'role:id,name', 'tenant:id,name'])
            ->where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $cutoff)
            ->orderBy('expires_at')
            ->get()
            ->groupBy('tenant_id');

        $tenantIds = collect($activeCounts->keys())
            ->merge($expiring->keys())
            ->unique()
            ->filter()
            ->values();

        if ($tenantIds->isEmpty()) {
            $this->info('No PAM activations found.');

            return self::SUCCESS;
        }

        $tenants = Tenant::query()->whereIn('id', $tenantIds)->get();

        foreach ($tenants as $tenant) {
            $totalActive = (int) ($activeCounts[$tenant->getKey()] ?? 0);
            $expiringItems = $expiring->get($tenant->getKey(), collect());

            if ($totalActive === 0 && $expiringItems->isEmpty()) {
                continue;
            }

            $payload = [
                'tenant_id' => $tenant->getKey(),
                'tenant_name' => $tenant->name,
                'total_active' => $totalActive,
                'expiring_soon' => $expiringItems->count(),
                'expiring_items' => $expiringItems->map(fn (PrivilegeActivation $activation) => [
                    'activation_id' => $activation->getKey(),
                    'user_id' => $activation->user_id,
                    'user_name' => $activation->user?->name,
                    'user_email' => $activation->user?->email,
                    'role_id' => $activation->role_id,
                    'role_name' => $activation->role?->name,
                    'expires_at' => $activation->expires_at?->toISOString(),
                ])->values()->toArray(),
            ];

            $recipients = $this->resolveRecipients($tenant);
            foreach ($recipients as $recipient) {
                $notificationService->sendNotification($recipient, 'pam.digest.weekly', $payload, $tenant);
            }
        }

        $this->info('PAM digest sent.');

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, mixed>
     */
    protected function resolveRecipients(Tenant $tenant): Collection
    {
        $roles = array_values(array_filter((array) config('filamat-iam.pam.digest.notify_roles', ['owner', 'admin'])));

        $users = $tenant->users()
            ->when($roles !== [], fn ($query) => $query->wherePivotIn('role', $roles))
            ->wherePivot('status', 'active')
            ->get();

        if ($tenant->owner) {
            $users->push($tenant->owner);
        }

        return $users->unique('id');
    }
}
