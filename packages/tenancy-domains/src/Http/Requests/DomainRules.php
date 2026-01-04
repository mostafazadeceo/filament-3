<?php

namespace Haida\TenancyDomains\Http\Requests;

final class DomainRules
{
    public static function hostRules(): array
    {
        return ['required', 'string', 'max:255', 'regex:/^[a-z0-9.-]+$/i'];
    }
}
