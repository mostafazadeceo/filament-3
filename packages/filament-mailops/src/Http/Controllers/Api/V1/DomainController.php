<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Http\Controllers\Api\V1;

use Filamat\IamSuite\Http\Controllers\Api\V1\BaseController;
use Haida\FilamentMailOps\Models\MailDomain;

class DomainController extends BaseController
{
    protected function modelClass(): string
    {
        return MailDomain::class;
    }

    protected function validationRules(string $action): array
    {
        $required = $action === 'store' ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'dkim_selector' => ['nullable', 'string', 'max:255'],
            'dkim_public_key' => ['nullable', 'string'],
            'comment' => ['nullable', 'string'],
        ];
    }
}
