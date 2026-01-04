<?php

declare(strict_types=1);

namespace Tests\Feature;

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Tests\TestCase;

class OpenApiScopeCoverageTest extends TestCase
{
    public function test_openapi_routes_require_auth_and_scope(): void
    {
        $routes = app('router')->getRoutes()->getRoutes();

        $openApiRoutes = collect($routes)->filter(
            static fn ($route): bool => str_contains($route->uri(), 'openapi')
        );

        $missing = [];
        $required = [ApiKeyAuth::class, ApiAuth::class, ResolveTenant::class];

        foreach ($openApiRoutes as $route) {
            $middleware = $route->gatherMiddleware();
            $missingRequired = [];

            foreach ($required as $middlewareClass) {
                $found = false;
                foreach ($middleware as $entry) {
                    if ($entry === $middlewareClass || str_ends_with($entry, '\\'.class_basename($middlewareClass))) {
                        $found = true;
                        break;
                    }
                }
                if (! $found) {
                    $missingRequired[] = $middlewareClass;
                }
            }

            $hasScope = false;
            foreach ($middleware as $entry) {
                if (is_string($entry) && str_starts_with($entry, 'filamat-iam.scope:')) {
                    $hasScope = true;
                    break;
                }
            }

            if (! $hasScope || $missingRequired !== []) {
                $missing[] = [
                    'uri' => $route->uri(),
                    'missing_scope' => ! $hasScope,
                    'missing_middleware' => $missingRequired,
                ];
            }
        }

        $this->assertSame([], $missing, 'OpenAPI routes missing scope or auth middleware: '.json_encode($missing));
    }
}
