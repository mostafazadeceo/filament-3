<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Replenishment;

use Haida\FilamentPettyCashIr\Domain\Rules\ReplenishmentRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class RejectReplenishment
{
    public function __construct(
        private readonly ReplenishmentRules $rules,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashReplenishment $replenishment, ?int $actorId = null): PettyCashReplenishment
    {
        $this->rules->assertCanReject($replenishment);

        $replenishment->update([
            'status' => PettyCashStatuses::REPLENISHMENT_REJECTED,
        ]);

        $this->auditLogger->log($replenishment, $actorId, 'replenishment_rejected', 'رد تغذیه');
        event(new PettyCashEvent('replenishment.rejected', $replenishment));

        return $replenishment->refresh();
    }
}
