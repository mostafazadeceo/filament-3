<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAudienceContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:190'],
            'name' => ['nullable', 'string', 'max:190'],
            'status' => ['nullable', Rule::in(['subscribed', 'unsubscribed'])],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
