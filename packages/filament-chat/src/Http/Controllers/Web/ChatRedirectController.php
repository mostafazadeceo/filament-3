<?php

namespace Haida\FilamentChat\Http\Controllers\Web;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentChat\Models\ChatConnection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            throw new NotFoundHttpException('Tenant not found.');
        }

        $connection = ChatConnection::query()
            ->where('tenant_id', $tenant->getKey())
            ->default()
            ->first();

        if (! $connection || ! $connection->base_url) {
            throw new NotFoundHttpException('Chat not configured.');
        }

        $base = rtrim((string) $connection->base_url, '/');
        if ($base === '') {
            throw new NotFoundHttpException('Chat not configured.');
        }

        $path = $request->path();
        $tail = ltrim((string) Str::after($path, 'chat'), '/');
        $target = $base.($tail !== '' ? '/'.$tail : '');

        if ($request->getQueryString()) {
            $target .= '?'.$request->getQueryString();
        }

        return redirect()->away($target);
    }
}
