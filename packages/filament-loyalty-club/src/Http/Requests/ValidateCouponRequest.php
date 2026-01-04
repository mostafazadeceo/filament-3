<?php

namespace Haida\FilamentLoyaltyClub\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class ValidateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('loyalty.coupon.view');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'code' => ['required', 'string'],
            'amount' => ['nullable', 'numeric'],
            'order_reference' => ['nullable', 'string'],
        ];
    }
}
