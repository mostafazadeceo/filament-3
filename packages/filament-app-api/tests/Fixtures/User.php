<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Tests\Fixtures;

use Filamat\IamSuite\Support\HasIamSuite;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasIamSuite;
    use HasRoles;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
