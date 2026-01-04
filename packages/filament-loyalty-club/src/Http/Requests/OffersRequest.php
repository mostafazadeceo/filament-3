<?php

namespace Haida\FilamentLoyaltyClub\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class OffersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('loyalty.campaign.view');
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
        ];
    }
}
