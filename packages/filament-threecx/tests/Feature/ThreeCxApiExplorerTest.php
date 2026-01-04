<?php

namespace Haida\FilamentThreeCx\Tests\Feature;

use Haida\FilamentThreeCx\Filament\Pages\ThreeCxApiExplorerPage;
use Haida\FilamentThreeCx\Tests\TestCase;
use ReflectionMethod;

class ThreeCxApiExplorerTest extends TestCase
{
    public function test_denylist_blocks_paths(): void
    {
        config(['filament-threecx.api_explorer.denylist' => ['recording', 'audio']]);

        $page = new ThreeCxApiExplorerPage;
        $method = new ReflectionMethod($page, 'isDeniedPath');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($page, '/recordings/123'));
        $this->assertTrue($method->invoke($page, '/audio/stream'));
        $this->assertFalse($method->invoke($page, '/contacts'));
    }
}
