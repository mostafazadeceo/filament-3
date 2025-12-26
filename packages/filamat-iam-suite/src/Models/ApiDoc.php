<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use ZPMLabs\FilamentApiDocsBuilder\Models\ApiDocs;
use ZPMLabs\FilamentApiDocsBuilder\Observers\ApiDocsObserver;

#[ObservedBy(ApiDocsObserver::class)]
class ApiDoc extends ApiDocs
{
    use BelongsToTenant;
}
