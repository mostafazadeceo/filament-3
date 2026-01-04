<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class AutomationActionProposalExecuted extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'automation.n8n.action_proposal.executed';
    }
}
