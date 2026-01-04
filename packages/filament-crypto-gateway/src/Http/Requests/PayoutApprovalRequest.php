<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayoutApprovalRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
