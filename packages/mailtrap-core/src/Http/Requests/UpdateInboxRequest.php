<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInboxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:150'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
        ];
    }
}
