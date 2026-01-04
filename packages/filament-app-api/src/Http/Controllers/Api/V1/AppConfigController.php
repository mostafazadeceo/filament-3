<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AppConfigController
{
    public function show(Request $request)
    {
        $config = config('filament-app-api.app_config', []);
        $turnServers = (string) ($config['turn_servers'] ?? '');

        $parsedTurn = [];
        if ($turnServers !== '') {
            $decoded = json_decode($turnServers, true);
            if (is_array($decoded)) {
                $parsedTurn = $decoded;
            } else {
                $parsedTurn = collect(explode(',', $turnServers))
                    ->map(fn ($value) => trim($value))
                    ->filter()
                    ->values()
                    ->all();
            }
        }

        return response()->json([
            'websocket_url' => (string) ($config['websocket_url'] ?? ''),
            'realtime_fallback' => (string) ($config['realtime_fallback'] ?? 'polling'),
            'turn_servers' => $parsedTurn,
            'features' => $config['features'] ?? [],
            'endpoints' => $config['endpoints'] ?? [],
            'base_url' => Str::finish(config('app.url', ''), '/'),
        ]);
    }
}
