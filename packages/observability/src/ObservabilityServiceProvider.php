<?php

declare(strict_types=1);

namespace Haida\Observability;

use Haida\Observability\Services\CorrelationIdFactory;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ObservabilityServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('observability')
            ->hasConfigFile('observability');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CorrelationIdFactory::class);
    }

    public function packageBooted(): void
    {
        if (! config('observability.enabled', true)) {
            return;
        }

        if ($this->app->runningInConsole()) {
            return;
        }

        /** @var Request $request */
        $request = $this->app['request'];
        $header = (string) config('observability.correlation.header', 'X-Correlation-Id');
        $contextKey = (string) config('observability.correlation.context_key', 'correlation_id');

        $correlationId = $this->app->make(CorrelationIdFactory::class)->fromRequest($request, $header);
        $this->app->instance('correlation_id', $correlationId);

        Log::withContext([
            $contextKey => $correlationId,
            'path' => $request->path(),
            'method' => $request->method(),
        ]);

        Event::listen(RequestHandled::class, function (RequestHandled $event) use ($header, $correlationId): void {
            $event->response->headers->set($header, $correlationId);
        });
    }
}
