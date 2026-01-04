<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Events;

use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EsimGoCatalogueSynced
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public EsimGoConnection $connection, public int $count)
    {
    }
}
