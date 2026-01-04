<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RunReconcileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'scope' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
