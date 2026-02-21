<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkPhonebook;
use Illuminate\Http\Request;

class PhonebookController extends ApiController
{
    public function index()
    {
        return $this->ok(['items' => SmsBulkPhonebook::query()->where('tenant_id', $this->tenantId())->get()->toArray()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        $item = SmsBulkPhonebook::create(['tenant_id' => $this->tenantId()] + $data);

        return $this->ok(['id' => $item->getKey()], 201);
    }

    public function show(int $id)
    {
        $item = SmsBulkPhonebook::query()->where('tenant_id', $this->tenantId())->findOrFail($id);

        return $this->ok($item->toArray());
    }

    public function update(Request $request, int $id)
    {
        $item = SmsBulkPhonebook::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->update($request->validate([
            'name' => ['sometimes', 'string', 'max:150'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]));

        return $this->ok(['id' => $item->getKey()]);
    }

    public function destroy(int $id)
    {
        $item = SmsBulkPhonebook::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->delete();

        return $this->ok();
    }
}
