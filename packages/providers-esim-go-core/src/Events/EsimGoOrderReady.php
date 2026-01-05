<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Events;

use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EsimGoOrderReady
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public EsimGoOrder $order) {}
}
