<?php

declare(strict_types=1);

namespace Tests\Unit\Observability;

use Haida\Observability\Services\CorrelationIdFactory;
use Illuminate\Http\Request;
use Tests\TestCase;

class CorrelationIdFactoryTest extends TestCase
{
    public function test_uses_header_value(): void
    {
        $factory = new CorrelationIdFactory();
        $request = Request::create('/', 'GET', server: [
            'HTTP_X_CORRELATION_ID' => 'test-correlation',
        ]);

        $value = $factory->fromRequest($request, 'X-Correlation-Id');

        $this->assertSame('test-correlation', $value);
    }

    public function test_generates_when_missing(): void
    {
        $factory = new CorrelationIdFactory();
        $request = Request::create('/', 'GET');

        $value = $factory->fromRequest($request, 'X-Correlation-Id');

        $this->assertNotSame('', $value);
    }
}
