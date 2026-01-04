<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\BaseController;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMailOps\Models\MailAlias;
use Illuminate\Validation\Rule;

class AliasController extends BaseController
{
    protected function modelClass(): string
    {
        return MailAlias::class;
    }

    protected function validationRules(string $action): array
    {
        $required = $action === 'store' ? 'required' : 'sometimes';
        $domainsTable = config('filament-mailops.tables.domains', 'mailops_domains');

        $domainRule = Rule::exists($domainsTable, 'id');
        if (! TenantContext::shouldBypass() && TenantContext::getTenantId()) {
            $domainRule = $domainRule->where('tenant_id', TenantContext::getTenantId());
        }

        return [
            'domain_id' => [$required, 'integer', $domainRule],
            'source' => [$required, 'email'],
            'destinations' => [$required, 'array', 'min:1'],
            'destinations.*' => ['email'],
            'is_wildcard' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'max:50'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
