<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Fixtures;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;

    protected $guarded = [];

    public function hasIamSuiteSuperAdmin(): bool
    {
        return (bool) ($this->iam_suite_super_admin ?? false);
    }

    public function currentAccessToken(): mixed
    {
        return null;
    }
}
