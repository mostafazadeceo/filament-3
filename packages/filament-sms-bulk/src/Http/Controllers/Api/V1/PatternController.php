<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkPatternTemplate;
use Illuminate\Http\Request;

class PatternController extends ApiController
{
    public function index()
    {
        return $this->ok(['items' => SmsBulkPatternTemplate::query()->where('tenant_id', $this->tenantId())->get()->toArray()]);
    }

    public function store(Request $request)
    {
        $item = SmsBulkPatternTemplate::create(
            ['tenant_id' => $this->tenantId()] +
            $request->validate([
                'provider_connection_id' => ['required', 'integer'],
                'pattern_code' => ['required', 'string', 'max:128'],
                'title_translations' => ['nullable', 'array'],
                'variables_schema' => ['nullable', 'array'],
                'status' => ['nullable', 'string', 'max:32'],
            ])
        );

        return $this->ok(['id' => $item->getKey()], 201);
    }

    public function show(int $id)
    {
        $item = SmsBulkPatternTemplate::query()->where('tenant_id', $this->tenantId())->findOrFail($id);

        return $this->ok($item->toArray());
    }

    public function update(Request $request, int $id)
    {
        $item = SmsBulkPatternTemplate::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->update($request->validate([
            'title_translations' => ['sometimes', 'nullable', 'array'],
            'variables_schema' => ['sometimes', 'nullable', 'array'],
            'status' => ['sometimes', 'string', 'max:32'],
        ]));

        return $this->ok(['id' => $item->getKey()]);
    }

    public function destroy(int $id)
    {
        $item = SmsBulkPatternTemplate::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->delete();

        return $this->ok();
    }
}
