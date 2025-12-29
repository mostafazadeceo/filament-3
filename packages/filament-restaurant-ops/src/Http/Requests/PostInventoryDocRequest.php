<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

class PostInventoryDocRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $doc = $this->route('inventory_doc');

        return $doc ? auth()->user()?->can('post', $doc) ?? false : false;
    }

    public function rules(): array
    {
        return [];
    }
}
