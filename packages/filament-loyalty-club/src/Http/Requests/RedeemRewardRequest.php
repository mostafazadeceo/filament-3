<?php

namespace Haida\FilamentLoyaltyClub\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class RedeemRewardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('loyalty.reward.redeem');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'payload' => ['nullable', 'array'],
        ];
    }
}
