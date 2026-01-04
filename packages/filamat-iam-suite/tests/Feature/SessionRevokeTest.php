<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\UserSession;
use Filamat\IamSuite\Services\SessionService;
use Filamat\IamSuite\Tests\Fixtures\User;
use Illuminate\Support\Facades\DB;

it('revokes sessions and deletes database session record', function () {
    config(['session.driver' => 'database']);

    $tenant = Tenant::query()->create(['name' => 'Session Tenant', 'slug' => 'session']);
    $user = User::query()->create(['name' => 'Session User', 'email' => 'session@example.com', 'password' => bcrypt('secret')]);

    DB::table('sessions')->insert([
        'id' => 'session-id-1',
        'user_id' => $user->getKey(),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test-agent',
        'payload' => 'payload',
        'last_activity' => now()->timestamp,
    ]);

    $session = UserSession::query()->create([
        'session_id' => 'session-id-1',
        'tenant_id' => $tenant->getKey(),
        'user_id' => $user->getKey(),
        'ip' => '127.0.0.1',
        'user_agent' => 'test-agent',
        'last_activity_at' => now(),
    ]);

    app(SessionService::class)->revoke($session, $user, 'admin');

    $exists = DB::table('sessions')->where('id', 'session-id-1')->exists();
    expect($exists)->toBeFalse();
    expect($session->fresh()->revoked_at)->not->toBeNull();
});
