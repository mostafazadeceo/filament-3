<?php

namespace Haida\FilamentLoyaltyClub\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('loyalty.event.ingest');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'type' => ['required', 'string'],
            'payload' => ['nullable', 'array'],
            'idempotency_key' => ['required', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:64'],
        ];
    }
}
