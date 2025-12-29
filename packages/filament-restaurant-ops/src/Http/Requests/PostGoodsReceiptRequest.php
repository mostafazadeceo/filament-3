<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

class PostGoodsReceiptRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $receipt = $this->route('goods_receipt');

        return $receipt ? auth()->user()?->can('post', $receipt) ?? false : false;
    }

    public function rules(): array
    {
        return [];
    }
}
