<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Controllers\Api\V1;

use Haida\FilamentCryptoCore\Models\CryptoRate;
use Haida\FilamentCryptoCore\Services\RateService;
use Haida\FilamentCryptoGateway\Http\Resources\CryptoRateResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RateController extends ApiController
{
    public function index(Request $request, RateService $service): AnonymousResourceCollection
    {
        $this->authorize('viewAny', CryptoRate::class);

        $from = (string) $request->query('from', 'USDT');
        $to = (string) $request->query('to', 'IRR');

        $rate = $service->getRate($from, $to);

        return CryptoRateResource::collection(collect(array_filter([$rate])));
    }
}
