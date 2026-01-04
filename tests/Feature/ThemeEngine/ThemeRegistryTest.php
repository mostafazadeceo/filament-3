<?php

namespace Tests\Feature\ThemeEngine;

use Haida\ThemeEngine\ThemeRegistry;
use Tests\TestCase;

class ThemeRegistryTest extends TestCase
{
    public function test_registry_contains_relograde_theme(): void
    {
        $registry = app(ThemeRegistry::class);

        $theme = $registry->get('relograde-v1');

        $this->assertNotNull($theme);
        $this->assertSame('relograde-v1', $theme->key);
        $this->assertSame('Relograde v1', $theme->name);
    }
}
