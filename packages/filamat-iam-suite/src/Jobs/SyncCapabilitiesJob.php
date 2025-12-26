<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Jobs;

use Filamat\IamSuite\Services\CapabilitySyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCapabilitiesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ?string $guard = null) {}

    public function handle(CapabilitySyncService $service): void
    {
        $service->sync($this->guard);
    }
}
