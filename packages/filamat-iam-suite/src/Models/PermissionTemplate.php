<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Models;

use Filamat\IamSuite\Support\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PermissionTemplate extends Model
{
    use BelongsToTenant;

    protected $guarded = [];

    protected $casts = [
        'permissions' => 'array',
        'meta' => 'array',
    ];
}
