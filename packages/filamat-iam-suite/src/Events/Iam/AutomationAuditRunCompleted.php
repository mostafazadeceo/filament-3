<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class AutomationAuditRunCompleted extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'automation.n8n.audit.run.completed';
    }
}
