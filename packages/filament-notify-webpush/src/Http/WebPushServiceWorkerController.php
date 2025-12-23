<?php

namespace Haida\FilamentNotify\WebPush\Http;

use Illuminate\Routing\Controller;

class WebPushServiceWorkerController extends Controller
{
    public function show()
    {
        $path = __DIR__ . '/../../resources/js/filament-notify-sw.js';
        if (! file_exists($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/javascript',
        ]);
    }
}
