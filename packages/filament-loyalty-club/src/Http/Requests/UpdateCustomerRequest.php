<?php

namespace Haida\FilamentLoyaltyClub\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('loyalty.customer.manage');
    }

    public function rules(): array
    {
        return [
            'tier_id' => ['nullable', 'integer'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'external_refs' => ['nullable', 'array'],
            'status' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date'],
            'marketing_opt_in' => ['nullable', 'boolean'],
            'marketing_opt_in_source' => ['nullable', 'string', 'max:255'],
            'sms_opt_in' => ['nullable', 'boolean'],
            'whatsapp_opt_in' => ['nullable', 'boolean'],
            'telegram_opt_in' => ['nullable', 'boolean'],
            'bale_opt_in' => ['nullable', 'boolean'],
            'webpush_opt_in' => ['nullable', 'boolean'],
            'email_opt_in' => ['nullable', 'boolean'],
        ];
    }
}
