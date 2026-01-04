<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domain_name' => ['sometimes', 'string', 'max:190'],
        ];
    }
}
