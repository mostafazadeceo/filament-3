<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

class PostMenuSaleRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $sale = $this->route('menu_sale');

        return $sale ? auth()->user()?->can('post', $sale) ?? false : false;
    }

    public function rules(): array
    {
        return [];
    }
}
