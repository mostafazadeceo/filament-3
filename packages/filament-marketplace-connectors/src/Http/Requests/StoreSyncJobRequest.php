<?php

namespace Haida\FilamentMarketplaceConnectors\Http\Requests;

use Haida\FilamentMarketplaceConnectors\Models\MarketplaceSyncJob;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSyncJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', MarketplaceSyncJob::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'job_type' => ['required', Rule::in(['catalog', 'inventory', 'orders'])],
        ];
    }
}
