<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KycDefenseController;
use Illuminate\Http\Request;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Filamat\IamSuite\Support\OrganizationAccess;

Route::get('/lang/{locale}', function (Request $request, string $locale) {
    $locale = strtolower(trim($locale));
    $allowed = ['fa', 'en', 'ar'];

    if (! in_array($locale, $allowed, true)) {
        abort(404);
    }

    $request->session()->put('abrak_locale', $locale);
    // Persist across browser restarts as well.
    cookie()->queue(cookie('abrak_locale', $locale, 60 * 24 * 365));

    $redirect = $request->query('redirect') ?: url()->previous();
    if (is_string($redirect) && $redirect !== '') {
        $parts = parse_url($redirect);
        $host = strtolower($parts['host'] ?? $request->getHost());
        $scheme = strtolower($parts['scheme'] ?? $request->getScheme());

        if ($host === strtolower($request->getHost()) && in_array($scheme, ['http', 'https'], true)) {
            return redirect()->to($redirect);
        }
    }

    return redirect()->to('/');
})->name('lang.switch');

Route::get('/', function (Request $request) {
    $user = $request->user();
    if ($user) {
        return MegaSuperAdmin::check($user)
            ? redirect()->to('/admin')
            : redirect()->to('/tenant');
    }

    return view('welcome');
});

Route::get('/login', function (Request $request) {
    $user = $request->user();
    if ($user) {
        return MegaSuperAdmin::check($user)
            ? redirect()->to('/admin')
            : redirect()->to('/tenant');
    }

    return redirect()->route('filament.tenant.auth.login');
})->name('login');

Route::get('/logout', function (Request $request) {
    // A lightweight, cross-app logout endpoint so abrak.org can link "Logout"
    // to hub and then return back to abrak without requiring a POST+CSRF.
    // Some auth event listeners assume an authenticated user instance.
    // Avoid triggering them when the request is already anonymous.
    if (auth()->check()) {
        auth()->logout();
    }

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $fallback = 'https://abrak.org/';
    $redirect = $request->query('redirect');

    if (is_string($redirect) && $redirect !== '') {
        $parts = parse_url($redirect);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');

        if ($scheme === 'https' && ($host === 'abrak.org' || str_ends_with($host, '.abrak.org'))) {
            return redirect()->to($redirect);
        }
    }

    return redirect()->to($fallback);
})->name('logout');

Route::match(['GET', 'OPTIONS'], '/auth/status', function (Request $request) {
    $origin = $request->headers->get('Origin');
    $allowedOrigins = [
        'https://abrak.org',
    ];

    $headers = [];
    if (is_string($origin) && in_array($origin, $allowedOrigins, true)) {
        // Allow abrak.org to detect hub login state (cookies are same-site).
        $headers['Access-Control-Allow-Origin'] = $origin;
        $headers['Access-Control-Allow-Credentials'] = 'true';
        $headers['Access-Control-Allow-Methods'] = 'GET, OPTIONS';
        $headers['Access-Control-Allow-Headers'] = 'Content-Type';
        $headers['Vary'] = 'Origin';
    }

    if ($request->isMethod('OPTIONS')) {
        return response('', 204)->withHeaders($headers);
    }

    $authed = auth()->check();
    $user = $authed ? auth()->user() : null;
    $tenant = null;
    $organization = null;

    if ($user && method_exists($user, 'tenants')) {
        $tenant = $user->tenants()
            ->wherePivot('status', 'active')
            ->first();

        if ($tenant) {
            $organization = OrganizationAccess::currentOrganization($tenant);
        }
    }

    return response()->json([
        'authenticated' => $authed,
        'user' => $user ? [
            'id' => $user->getKey(),
            'name' => (string) ($user->name ?? ''),
        ] : null,
        'tenant' => $tenant ? [
            'id' => $tenant->getKey(),
            'name' => (string) ($tenant->name ?? ''),
            'slug' => (string) ($tenant->slug ?? ''),
        ] : null,
        'organization' => $organization ? [
            'id' => $organization->getKey(),
            'name' => (string) ($organization->name ?? ''),
        ] : null,
        'is_superadmin' => $user ? MegaSuperAdmin::check($user) : false,
    ])->withHeaders($headers);
})->name('auth.status');

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/ttt', [KycDefenseController::class, 'index'])->name('ttt.dashboard');
    Route::post('/ttt/analyze', [KycDefenseController::class, 'analyze'])->name('ttt.analyze');
});
