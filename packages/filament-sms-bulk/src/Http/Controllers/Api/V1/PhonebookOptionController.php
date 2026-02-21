<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkPhonebookOption;
use Illuminate\Http\Request;

class PhonebookOptionController extends ApiController
{
    public function index(Request $request)
    {
        $phonebookId = (int) $request->query('phonebook_id');

        $query = SmsBulkPhonebookOption::query()->where('tenant_id', $this->tenantId());
        if ($phonebookId > 0) {
            $query->where('phonebook_id', $phonebookId);
        }

        return $this->ok(['items' => $query->get()->toArray()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'phonebook_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:150'],
            'type' => ['required', 'string', 'max:16'],
            'is_required' => ['nullable', 'boolean'],
        ]);

        $item = SmsBulkPhonebookOption::create(['tenant_id' => $this->tenantId()] + $data);

        return $this->ok(['id' => $item->getKey()], 201);
    }

    public function update(Request $request, int $id)
    {
        $item = SmsBulkPhonebookOption::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->update($request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'type' => ['sometimes', 'string', 'max:16'],
            'is_required' => ['sometimes', 'boolean'],
        ]));

        return $this->ok(['id' => $item->getKey()]);
    }

    public function destroy(int $id)
    {
        $item = SmsBulkPhonebookOption::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->delete();

        return $this->ok();
    }
}
