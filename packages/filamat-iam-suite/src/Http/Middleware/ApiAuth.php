<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) config('filamat-iam.api.api_key_header', 'X-Api-Key');

        if ($request->header($header)) {
            return $next($request);
        }

        if (! $request->user() && ! Auth::guard('sanctum')->user()) {
            return response()->json(['message' => 'دسترسی غیرمجاز.'], 401);
        }

        return $next($request);
    }
}
