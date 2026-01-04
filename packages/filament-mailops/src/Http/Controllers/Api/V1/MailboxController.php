<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\BaseController;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentMailOps\Models\MailMailbox;
use Illuminate\Validation\Rule;

class MailboxController extends BaseController
{
    protected function modelClass(): string
    {
        return MailMailbox::class;
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
            'local_part' => [$required, 'string', 'max:255'],
            'password' => [$required, 'string', 'min:6'],
            'display_name' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'quota_bytes' => ['nullable', 'integer', 'min:0'],
            'settings' => ['nullable', 'array'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
