<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Automation;

use Filamat\IamSuite\Contracts\IamEvent;
use Filamat\IamSuite\Events\Iam\AutomationActionProposalApproved;
use Filamat\IamSuite\Events\Iam\AutomationActionProposalExecuted;
use Filamat\IamSuite\Events\Iam\AutomationActionProposalReceived;
use Filamat\IamSuite\Events\Iam\AutomationActionProposalRejected;
use Filamat\IamSuite\Events\Iam\AutomationAuditRunCompleted;
use Filamat\IamSuite\Events\Iam\AutomationAuditRunStarted;
use Filamat\IamSuite\Events\Iam\AutomationCallbackFailed;
use Filamat\IamSuite\Events\Iam\AutomationReportReceived;
use Filamat\IamSuite\Events\Iam\IamAccessRequestApproved;
use Filamat\IamSuite\Events\Iam\IamAccessRequestCreated;
use Filamat\IamSuite\Events\Iam\IamAccessRequestRejected;
use Filamat\IamSuite\Events\Iam\IamMembershipRoleAssigned;
use Filamat\IamSuite\Events\Iam\IamMembershipRoleRevoked;
use Filamat\IamSuite\Events\Iam\IamPermissionOverrideChanged;
use Filamat\IamSuite\Events\Iam\IamUserCreated;
use Filamat\IamSuite\Events\Iam\IamUserDeleted;
use Filamat\IamSuite\Events\Iam\IamUserUpdated;
use Filamat\IamSuite\Events\Iam\SecurityApiKeyCreated;
use Filamat\IamSuite\Events\Iam\SecurityApiKeyRevoked;
use Filamat\IamSuite\Events\Iam\SecurityApiKeyRotated;
use Filamat\IamSuite\Events\Iam\SecurityAuthLoginFailed;
use Filamat\IamSuite\Events\Iam\SecurityAuthLoginSucceeded;
use Filamat\IamSuite\Events\Iam\SecurityAuthLogout;
use Filamat\IamSuite\Events\Iam\SecurityImpersonationStarted;
use Filamat\IamSuite\Events\Iam\SecurityImpersonationStopped;
use Filamat\IamSuite\Events\Iam\SecurityOtpFailed;
use Filamat\IamSuite\Events\Iam\SecurityOtpRequested;
use Filamat\IamSuite\Events\Iam\SecurityOtpVerified;
use Filamat\IamSuite\Events\Iam\SubscriptionCanceled;
use Filamat\IamSuite\Events\Iam\SubscriptionCreated;
use Filamat\IamSuite\Events\Iam\SubscriptionRenewed;
use Filamat\IamSuite\Events\Iam\WalletTransactionCreated;
use Filamat\IamSuite\Models\AccessRequest;
use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\SecurityEvent;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\WalletTransaction;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class IamEventFactory
{
    public function fromAudit(
        string $action,
        ?Model $subject,
        array $diff,
        ?Authenticatable $actor,
        ?Tenant $tenant
    ): ?IamEvent {
        $tenantId = $tenant?->getKey();
        $context = $this->contextPayload();
        $actorPayload = $this->actorPayload($actor);
        $occurredAt = now()->toIso8601String();

        if (in_array($action, ['access_request.created', 'access_request.approved', 'access_request.denied'], true) && $subject instanceof AccessRequest) {
            $payload = [
                'actor' => $actorPayload,
                'subject' => [
                    'type' => 'access_request',
                    'id' => $subject->getKey(),
                ],
                'context' => $context,
                'data' => [
                    'status' => $subject->status,
                    'user_id' => $subject->user_id,
                    'requested_permissions' => $subject->requested_permissions,
                    'requested_roles' => $subject->requested_roles,
                    'reason' => $subject->reason,
                ],
                'occurred_at' => $subject->created_at?->toIso8601String() ?? $occurredAt,
            ];

            return match ($action) {
                'access_request.created' => new IamAccessRequestCreated($tenantId, $payload),
                'access_request.approved' => new IamAccessRequestApproved($tenantId, $payload),
                default => new IamAccessRequestRejected($tenantId, $payload),
            };
        }

        if (in_array($action, ['role.user.attached', 'role.user.detached'], true) && $subject instanceof Role) {
            $userId = $diff['user_id'] ?? null;
            $tenantId = $tenantId ?? ($diff['tenant_id'] ?? null);

            $payload = [
                'actor' => $actorPayload,
                'subject' => [
                    'type' => 'user',
                    'id' => $userId,
                ],
                'context' => $context,
                'data' => [
                    'role' => [
                        'id' => $subject->getKey(),
                        'name' => $subject->name,
                    ],
                    'tenant_id' => $tenantId,
                ],
                'occurred_at' => $occurredAt,
            ];

            return $action === 'role.user.attached'
                ? new IamMembershipRoleAssigned($tenantId, $payload)
                : new IamMembershipRoleRevoked($tenantId, $payload);
        }

        if ($subject instanceof PermissionOverride && in_array($action, ['created', 'updated', 'deleted'], true)) {
            $payload = [
                'actor' => $actorPayload,
                'subject' => [
                    'type' => 'permission_override',
                    'id' => $subject->getKey(),
                ],
                'context' => $context,
                'data' => [
                    'user_id' => $subject->user_id,
                    'permission_key' => $subject->permission_key,
                    'effect' => $subject->effect,
                    'expires_at' => $subject->expires_at?->toIso8601String(),
                    'changes' => $diff,
                ],
                'occurred_at' => $occurredAt,
            ];

            return new IamPermissionOverrideChanged($tenantId ?? $subject->tenant_id, $payload);
        }

        if ($subject instanceof ApiKey && in_array($action, ['created', 'updated', 'deleted'], true)) {
            $payload = [
                'actor' => $actorPayload,
                'subject' => [
                    'type' => 'api_key',
                    'id' => $subject->getKey(),
                ],
                'context' => $context,
                'data' => [
                    'name' => $subject->name,
                    'token_prefix' => $subject->token_prefix,
                    'scopes' => $subject->effectiveScopes(),
                ],
                'occurred_at' => $occurredAt,
            ];

            if ($action === 'created') {
                return new SecurityApiKeyCreated($tenantId ?? $subject->tenant_id, $payload);
            }

            if ($action === 'deleted') {
                return new SecurityApiKeyRevoked($tenantId ?? $subject->tenant_id, $payload);
            }

            $changes = $diff['after'] ?? $diff;
            if (is_array($changes) && (array_key_exists('token_hash', $changes) || array_key_exists('token_prefix', $changes))) {
                return new SecurityApiKeyRotated($tenantId ?? $subject->tenant_id, $payload);
            }
        }

        if ($subject instanceof WalletTransaction && $action === 'created') {
            $payload = [
                'actor' => $actorPayload,
                'subject' => [
                    'type' => 'wallet_tx',
                    'id' => $subject->getKey(),
                ],
                'context' => $context,
                'data' => [
                    'type' => $subject->type,
                    'amount' => $subject->amount,
                    'status' => $subject->status,
                    'currency' => $subject->wallet?->currency,
                ],
                'occurred_at' => $subject->created_at?->toIso8601String() ?? $occurredAt,
            ];

            return new WalletTransactionCreated($tenantId ?? $subject->wallet?->tenant_id, $payload);
        }

        return null;
    }

    public function fromSecurityEvent(SecurityEvent $event): ?IamEvent
    {
        $tenantId = $event->tenant_id;
        $context = $this->contextPayload();
        $actorPayload = $this->actorPayload($event->user, $event->ip, $event->user_agent);
        $occurredAt = $event->occurred_at?->toIso8601String() ?? now()->toIso8601String();

        $basePayload = [
            'actor' => $actorPayload,
            'context' => $context,
            'occurred_at' => $occurredAt,
        ];

        return match ($event->type) {
            'auth.login' => new SecurityAuthLoginSucceeded($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->user_id],
                'data' => $event->meta ?? [],
            ])),
            'auth.logout' => new SecurityAuthLogout($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->user_id],
                'data' => $event->meta ?? [],
            ])),
            'auth.failed' => new SecurityAuthLoginFailed($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->user_id],
                'data' => $event->meta ?? [],
            ])),
            'otp.requested' => new SecurityOtpRequested($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->user_id],
                'data' => $event->meta ?? [],
            ])),
            'otp.verified' => new SecurityOtpVerified($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->user_id],
                'data' => $event->meta ?? [],
            ])),
            'otp.failed' => new SecurityOtpFailed($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->user_id],
                'data' => $event->meta ?? [],
            ])),
            'impersonation.start' => new SecurityImpersonationStarted($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->meta['target_id'] ?? null],
                'data' => $event->meta ?? [],
            ])),
            'impersonation.stop' => new SecurityImpersonationStopped($tenantId, array_merge($basePayload, [
                'subject' => ['type' => 'user', 'id' => $event->meta['target_id'] ?? null],
                'data' => $event->meta ?? [],
            ])),
            default => null,
        };
    }

    public function fromUserCreated(Authenticatable $user, ?Tenant $tenant, ?Authenticatable $actor = null): IamEvent
    {
        $tenantId = $tenant?->getKey() ?? TenantContext::getTenantId();
        $payload = [
            'actor' => $this->actorPayload($actor),
            'subject' => [
                'type' => 'user',
                'id' => $user->getAuthIdentifier(),
                'name' => $user->name ?? null,
                'email_masked' => $this->maskEmail($user->email ?? null),
            ],
            'context' => $this->contextPayload(),
            'occurred_at' => now()->toIso8601String(),
        ];

        return new IamUserCreated($tenantId, $payload);
    }

    public function fromUserUpdated(Authenticatable $user, array $changes, ?Tenant $tenant, ?Authenticatable $actor = null): IamEvent
    {
        $tenantId = $tenant?->getKey() ?? TenantContext::getTenantId();
        $payload = [
            'actor' => $this->actorPayload($actor),
            'subject' => [
                'type' => 'user',
                'id' => $user->getAuthIdentifier(),
                'name' => $user->name ?? null,
                'email_masked' => $this->maskEmail($user->email ?? null),
            ],
            'context' => $this->contextPayload(),
            'data' => [
                'changes' => $changes,
            ],
            'occurred_at' => now()->toIso8601String(),
        ];

        return new IamUserUpdated($tenantId, $payload);
    }

    public function fromUserDeleted(Authenticatable $user, ?Tenant $tenant, ?Authenticatable $actor = null): IamEvent
    {
        $tenantId = $tenant?->getKey() ?? TenantContext::getTenantId();
        $payload = [
            'actor' => $this->actorPayload($actor),
            'subject' => [
                'type' => 'user',
                'id' => $user->getAuthIdentifier(),
                'name' => $user->name ?? null,
                'email_masked' => $this->maskEmail($user->email ?? null),
            ],
            'context' => $this->contextPayload(),
            'occurred_at' => now()->toIso8601String(),
        ];

        return new IamUserDeleted($tenantId, $payload);
    }

    public function fromSubscription(Subscription $subscription): ?IamEvent
    {
        $tenantId = $subscription->tenant_id;
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'subscription',
                'id' => $subscription->getKey(),
            ],
            'context' => $this->contextPayload('system'),
            'data' => [
                'status' => $subscription->status,
                'plan_id' => $subscription->plan_id,
                'renews_at' => $subscription->renews_at?->toIso8601String(),
                'ends_at' => $subscription->ends_at?->toIso8601String(),
            ],
            'occurred_at' => now()->toIso8601String(),
        ];

        if ($subscription->wasRecentlyCreated) {
            return new SubscriptionCreated($tenantId, $payload);
        }

        if ($subscription->status === 'cancelled') {
            return new SubscriptionCanceled($tenantId, $payload);
        }

        if ($subscription->wasChanged('renews_at')) {
            return new SubscriptionRenewed($tenantId, $payload);
        }

        return null;
    }

    public function fromAutomationAuditStarted(?int $tenantId, array $summary): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'n8n_audit_run',
                'id' => $summary['run_id'] ?? null,
            ],
            'context' => $this->contextPayload('job'),
            'data' => $summary,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationAuditRunStarted($tenantId, $payload);
    }

    public function fromAutomationAuditCompleted(?int $tenantId, array $summary): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'n8n_audit_run',
                'id' => $summary['run_id'] ?? null,
            ],
            'context' => $this->contextPayload('job'),
            'data' => $summary,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationAuditRunCompleted($tenantId, $payload);
    }

    public function fromAutomationReportReceived(?int $tenantId, array $report): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'ai_report',
                'id' => $report['id'] ?? null,
            ],
            'context' => $this->contextPayload('webhook'),
            'data' => $report,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationReportReceived($tenantId, $payload);
    }

    public function fromAutomationProposalReceived(?int $tenantId, array $proposal): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'ai_action_proposal',
                'id' => $proposal['id'] ?? null,
            ],
            'context' => $this->contextPayload('webhook'),
            'data' => $proposal,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationActionProposalReceived($tenantId, $payload);
    }

    public function fromAutomationProposalApproved(?int $tenantId, array $proposal): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'ai_action_proposal',
                'id' => $proposal['id'] ?? null,
            ],
            'context' => $this->contextPayload('ui'),
            'data' => $proposal,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationActionProposalApproved($tenantId, $payload);
    }

    public function fromAutomationProposalRejected(?int $tenantId, array $proposal): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'ai_action_proposal',
                'id' => $proposal['id'] ?? null,
            ],
            'context' => $this->contextPayload('ui'),
            'data' => $proposal,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationActionProposalRejected($tenantId, $payload);
    }

    public function fromAutomationProposalExecuted(?int $tenantId, array $proposal): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null),
            'subject' => [
                'type' => 'ai_action_proposal',
                'id' => $proposal['id'] ?? null,
            ],
            'context' => $this->contextPayload('system'),
            'data' => $proposal,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationActionProposalExecuted($tenantId, $payload);
    }

    public function fromAutomationCallbackFailed(?int $tenantId, array $failure): IamEvent
    {
        $payload = [
            'actor' => $this->actorPayload(null, $failure['ip'] ?? null, null),
            'subject' => [
                'type' => 'n8n_callback',
                'id' => $failure['correlation_id'] ?? null,
            ],
            'context' => $this->contextPayload('webhook'),
            'data' => $failure,
            'occurred_at' => now()->toIso8601String(),
        ];

        return new AutomationCallbackFailed($tenantId, $payload);
    }

    /**
     * @return array<string, mixed>
     */
    protected function contextPayload(?string $source = null): array
    {
        $request = request();

        $resolvedSource = $source;
        if (! $resolvedSource) {
            $resolvedSource = app()->runningInConsole() ? 'job' : ($request ? 'ui' : 'system');
        }

        $context = [
            'source' => $resolvedSource,
        ];

        if ($request) {
            $traceId = $request->header('X-Trace-Id');
            if ($traceId) {
                $context['trace_id'] = $traceId;
            }

            $requestId = $request->header('X-Request-Id');
            if ($requestId) {
                $context['request_id'] = $requestId;
            }
        }

        return $context;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function actorPayload(?Authenticatable $actor = null, ?string $ip = null, ?string $ua = null): ?array
    {
        if (! $actor && ! $ip && ! $ua) {
            return null;
        }

        $payload = [
            'type' => $actor ? 'user' : 'system',
            'user_id' => $actor?->getAuthIdentifier(),
        ];

        if ($actor && isset($actor->email)) {
            $payload['email_masked'] = $this->maskEmail((string) $actor->email);
        }

        $payload['ip'] = $ip ?? request()?->ip();
        $payload['ua'] = $ua ?? request()?->userAgent();

        return $payload;
    }

    protected function maskEmail(?string $email): ?string
    {
        if (! $email) {
            return null;
        }

        if (! str_contains($email, '@')) {
            return '***';
        }

        [$name, $domain] = explode('@', $email, 2);

        if ($name === '') {
            return '***@'.$domain;
        }

        return substr($name, 0, 1).'***@'.$domain;
    }
}
