<?php

declare(strict_types=1);

use Filamat\IamSuite\Tests\TestCase as IamTestCase;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

spl_autoload_register(function (string $class): void {
    $prefix = 'Filamat\\IamSuite\\Tests\\';
    if (! str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = __DIR__.'/../packages/filamat-iam-suite/tests/'.str_replace('\\', '/', $relative).'.php';
    if (is_file($path)) {
        require $path;
    }
});

if (is_dir(__DIR__.'/../packages/filamat-iam-suite/tests')) {
    uses(IamTestCase::class)->in(__DIR__.'/../packages/filamat-iam-suite/tests');
}
