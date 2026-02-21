<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkContact;
use Illuminate\Http\Request;

class ContactController extends ApiController
{
    public function index(Request $request)
    {
        $phonebookId = (int) $request->query('phonebook_id');

        $query = SmsBulkContact::query()->where('tenant_id', $this->tenantId());
        if ($phonebookId > 0) {
            $query->where('phonebook_id', $phonebookId);
        }

        return $this->ok(['items' => $query->paginate(100)->items()]);
    }

    public function store(Request $request)
    {
        $item = SmsBulkContact::create(
            ['tenant_id' => $this->tenantId()] +
            $request->validate([
                'phonebook_id' => ['required', 'integer'],
                'msisdn' => ['required', 'string', 'max:32'],
                'full_name' => ['nullable', 'string', 'max:150'],
                'option_values' => ['nullable', 'array'],
            ])
        );

        return $this->ok(['id' => $item->getKey()], 201);
    }

    public function update(Request $request, int $id)
    {
        $item = SmsBulkContact::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->update($request->validate([
            'full_name' => ['sometimes', 'nullable', 'string', 'max:150'],
            'option_values' => ['sometimes', 'nullable', 'array'],
        ]));

        return $this->ok(['id' => $item->getKey()]);
    }

    public function destroy(int $id)
    {
        $item = SmsBulkContact::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->delete();

        return $this->ok();
    }
}
