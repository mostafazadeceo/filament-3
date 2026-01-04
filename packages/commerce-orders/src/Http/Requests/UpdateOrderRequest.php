<?php

namespace Haida\CommerceOrders\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', 'in:pending,processing,fulfilled,cancelled,refunded'],
            'payment_status' => ['nullable', 'string', 'in:pending,paid,failed,refunded,partially_refunded'],
            'internal_note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
