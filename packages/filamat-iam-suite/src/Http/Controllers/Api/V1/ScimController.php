<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ScimController
{
    protected function guardEnabled(): ?Response
    {
        if (! (bool) config('filamat-iam.scim.enabled', false)) {
            return response(['message' => 'SCIM غیرفعال است.'], 501);
        }

        return null;
    }

    public function users(Request $request): Response
    {
        if ($resp = $this->guardEnabled()) {
            return $resp;
        }

        return response(['Resources' => [], 'totalResults' => 0], 200);
    }

    public function createUser(Request $request): Response
    {
        if ($resp = $this->guardEnabled()) {
            return $resp;
        }

        return response(['message' => 'SCIM user create scaffold'], 200);
    }

    public function updateUser(Request $request, string $id): Response
    {
        if ($resp = $this->guardEnabled()) {
            return $resp;
        }

        return response(['message' => 'SCIM user update scaffold'], 200);
    }

    public function deleteUser(string $id): Response
    {
        if ($resp = $this->guardEnabled()) {
            return $resp;
        }

        return response(['message' => 'SCIM user delete scaffold'], 200);
    }

    public function groups(Request $request): Response
    {
        if ($resp = $this->guardEnabled()) {
            return $resp;
        }

        return response(['Resources' => [], 'totalResults' => 0], 200);
    }
}
