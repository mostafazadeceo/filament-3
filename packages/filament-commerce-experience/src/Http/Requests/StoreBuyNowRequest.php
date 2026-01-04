<?php

namespace Haida\FilamentCommerceExperience\Http\Requests;

use Haida\FilamentCommerceExperience\Models\ExperienceBuyNowPreference;

class StoreBuyNowRequest extends BaseExperienceRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', ExperienceBuyNowPreference::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer'],
            'default_address_id' => ['nullable', 'integer'],
            'default_payment_provider' => ['nullable', 'string', 'max:64'],
            'requires_2fa' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
