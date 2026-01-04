<?php

namespace Haida\FilamentLoyaltyClub\Tests\Fixtures;

use Filamat\IamSuite\Support\HasIamSuite;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasIamSuite;
    use HasRoles;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
