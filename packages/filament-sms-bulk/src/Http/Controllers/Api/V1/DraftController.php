<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkDraftMessage;
use Illuminate\Http\Request;

class DraftController extends ApiController
{
    public function index()
    {
        return $this->ok(['items' => SmsBulkDraftMessage::query()->where('tenant_id', $this->tenantId())->get()->toArray()]);
    }

    public function store(Request $request)
    {
        $item = SmsBulkDraftMessage::create(
            ['tenant_id' => $this->tenantId()] +
            $request->validate([
                'draft_group_id' => ['required', 'integer'],
                'title_translations' => ['nullable', 'array'],
                'body_translations' => ['required', 'array'],
                'language' => ['nullable', 'string', 'max:8'],
            ])
        );

        return $this->ok(['id' => $item->getKey()], 201);
    }

    public function show(int $id)
    {
        $item = SmsBulkDraftMessage::query()->where('tenant_id', $this->tenantId())->findOrFail($id);

        return $this->ok($item->toArray());
    }

    public function update(Request $request, int $id)
    {
        $item = SmsBulkDraftMessage::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->update($request->validate([
            'title_translations' => ['sometimes', 'nullable', 'array'],
            'body_translations' => ['sometimes', 'array'],
            'language' => ['sometimes', 'string', 'max:8'],
        ]));

        return $this->ok(['id' => $item->getKey()]);
    }

    public function destroy(int $id)
    {
        $item = SmsBulkDraftMessage::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->delete();

        return $this->ok();
    }
}
