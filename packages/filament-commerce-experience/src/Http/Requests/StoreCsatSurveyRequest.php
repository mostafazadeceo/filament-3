<?php

namespace Haida\FilamentCommerceExperience\Http\Requests;

use Haida\FilamentCommerceExperience\Models\ExperienceCsatSurvey;

class StoreCsatSurveyRequest extends BaseExperienceRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', ExperienceCsatSurvey::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'order_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'channel' => ['nullable', 'string', 'max:64'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
