<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Console\Commands;

use Haida\FilamentAppApi\Support\AppOpenApi;
use Illuminate\Console\Command;

class AppApiOpenApiCommand extends Command
{
    protected $signature = 'app-api:openapi {--path=}';

    protected $description = 'Dump App API OpenAPI spec to stdout or file.';

    public function handle(): int
    {
        $spec = json_encode(AppOpenApi::toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if (! $spec) {
            $this->error('OpenAPI generation failed.');
            return self::FAILURE;
        }

        $path = $this->option('path');
        if ($path) {
            file_put_contents($path, $spec);
            $this->info("OpenAPI written to {$path}");
            return self::SUCCESS;
        }

        $this->line($spec);
        return self::SUCCESS;
    }
}
