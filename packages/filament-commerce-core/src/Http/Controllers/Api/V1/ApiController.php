<?php

namespace Haida\FilamentCommerceCore\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;

class ApiController extends Controller
{
    use AuthorizesRequests;

    protected function parseSince(Request $request): ?Carbon
    {
        $since = $request->query('since');
        if (! is_string($since) || $since === '') {
            return null;
        }

        try {
            return Carbon::parse($since);
        } catch (\Throwable) {
            return null;
        }
    }
}
