<?php

namespace App\Http\Controllers\Filament;

use App\Support\Navigation\AppContext;
use App\Support\Navigation\AppNavigationCatalog;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppSwitchController
{
    public function __invoke(Request $request, ?string $key = null): RedirectResponse
    {
        $panel = Filament::getCurrentPanel();

        if (! $panel) {
            return redirect('/');
        }

        if (! is_string($key) || $key === '') {
            AppContext::set(null);
            return redirect()->to(AppNavigationCatalog::homeUrl($panel));
        }

        try {
            $apps = AppNavigationCatalog::appsFromItems(AppNavigationCatalog::collectItems($panel));
        } catch (\Throwable) {
            $apps = [];
        }

        if (! isset($apps[$key])) {
            AppContext::set(null);
            return redirect()->to(AppNavigationCatalog::homeUrl($panel));
        }

        AppContext::set($key);

        try {
            $target = AppNavigationCatalog::firstUrlForApp($key, $panel);
        } catch (\Throwable) {
            $target = null;
        }

        $target ??= AppNavigationCatalog::homeUrl($panel);

        return redirect()->to($target);
    }
}
