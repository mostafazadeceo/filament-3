<?php

namespace Haida\FilamentLoyaltyClub\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class StoreReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('loyalty.referral.manage');
    }

    public function rules(): array
    {
        return [
            'program_id' => ['required', 'integer'],
            'referrer_customer_id' => ['required', 'integer'],
            'referee_phone' => ['nullable', 'string', 'max:20'],
            'referee_email' => ['nullable', 'email', 'max:255'],
            'source' => ['nullable', 'string', 'max:50'],
            'campaign_id' => ['nullable', 'integer'],
        ];
    }
}
