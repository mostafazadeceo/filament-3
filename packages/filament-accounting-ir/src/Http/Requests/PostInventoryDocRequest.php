<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Vendor\FilamentAccountingIr\Models\InventoryDoc;

class PostInventoryDocRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $doc = $this->route('inventory_doc');

        return $doc instanceof InventoryDoc
            && (auth()->user()?->can('post', $doc) ?? false);
    }

    public function rules(): array
    {
        return [];
    }
}
