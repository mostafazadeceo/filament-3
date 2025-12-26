<?php

declare(strict_types=1);

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\OtpService;
use Filamat\IamSuite\Tests\Fixtures\User;
use Illuminate\Support\Facades\Queue;

it('locks otp after max attempts', function () {
    Queue::fake();

    $user = User::query()->create(['name' => 'Otp', 'email' => 'otp@example.com', 'password' => bcrypt('secret')]);
    $tenant = Tenant::query()->create(['name' => 'OtpTenant', 'slug' => 'otp']);

    $service = app(OtpService::class);
    $otp = $service->create($user, 'login', $tenant);

    $maxAttempts = (int) config('filamat-iam.otp.max_attempts', 5);
    for ($i = 0; $i < $maxAttempts; $i++) {
        expect($service->verify($user, 'login', '000000', $tenant))->toBeFalse();
    }

    $otp->refresh();

    expect($otp->locked_until)->not->toBeNull();
    expect($otp->locked_until->isFuture())->toBeTrue();
});
