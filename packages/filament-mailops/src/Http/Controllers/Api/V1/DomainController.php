<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\BaseController;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMailOps\Models\MailDomain;
use Illuminate\Validation\Rule;

class DomainController extends BaseController
{
    private const DOMAIN_REGEX = '/^(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\\.(?!-)[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)+$/i';

    protected function modelClass(): string
    {
        return MailDomain::class;
    }

    protected function validationRules(string $action): array
    {
        $required = $action === 'store' ? 'required' : 'sometimes';
        $tenantId = TenantContext::getTenantId();

        $domainId = null;
        if ($action === 'update') {
            $domainId = request()->route('domain');
            $domainId = is_numeric($domainId) ? (int) $domainId : null;
        }

        $unique = Rule::unique(config('filament-mailops.tables.domains', 'mailops_domains'), 'name');
        if ($tenantId) {
            $unique = $unique->where(fn ($query) => $query->where('tenant_id', $tenantId));
        }
        if ($domainId) {
            $unique = $unique->ignore($domainId);
        }

        return [
            'name' => [
                $required,
                'string',
                'max:253',
                'regex:'.self::DOMAIN_REGEX,
                $unique,
            ],
            'status' => ['sometimes', Rule::in(['active', 'inactive', 'pending', 'failed'])],
            'dkim_selector' => ['nullable', 'string', 'max:63', 'regex:/^[a-z0-9._-]+$/i'],
            'dkim_public_key' => ['nullable', 'string', 'max:12000'],
            'comment' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
