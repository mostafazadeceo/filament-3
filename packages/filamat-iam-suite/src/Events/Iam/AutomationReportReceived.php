<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class AutomationReportReceived extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'automation.n8n.report.received';
    }
}
