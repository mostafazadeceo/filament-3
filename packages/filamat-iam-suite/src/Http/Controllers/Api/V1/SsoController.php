<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SsoController
{
    protected function guardEnabled(): ?Response
    {
        if (! (bool) config('filamat-iam.sso.enabled', false)) {
            return response(['message' => 'SSO غیرفعال است.'], 501);
        }

        return null;
    }

    public function providers(): Response
    {
        if ($resp = $this->guardEnabled()) {
            return $resp;
        }

        return response([
            'data' => [
                'oidc' => (bool) config('filamat-iam.sso.providers.oidc', true),
                'saml' => (bool) config('filamat-iam.sso.providers.saml', false),
            ],
        ], 200);
    }

    public function oidcCallback(Request $request): Response
    {
        if ($resp = $this->guardEnabled()) {
            return $resp;
        }

        return response(['message' => 'OIDC callback scaffold'], 200);
    }
}
