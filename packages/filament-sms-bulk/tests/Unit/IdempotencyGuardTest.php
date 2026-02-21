<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Unit;

use Haida\SmsBulk\Support\IdempotencyGuard;
use Haida\SmsBulk\Tests\TestCase;

class IdempotencyGuardTest extends TestCase
{
    public function test_once_executes_only_once_for_same_key_until_release(): void
    {
        $guard = app(IdempotencyGuard::class);

        $first = $guard->once('abc', 30, fn () => 'ok');
        $second = $guard->once('abc', 30, fn () => 'again');

        $this->assertSame('ok', $first);
        $this->assertNull($second);
    }
}
