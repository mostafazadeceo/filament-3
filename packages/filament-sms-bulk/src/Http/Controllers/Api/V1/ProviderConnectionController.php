<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Illuminate\Http\Request;

class ProviderConnectionController extends ApiController
{
    public function index()
    {
        $items = SmsBulkProviderConnection::query()->where('tenant_id', $this->tenantId())->get()->map(fn ($item) => [
            'id' => $item->id,
            'provider' => $item->provider,
            'display_name' => $item->display_name,
            'base_url_override' => $item->base_url_override,
            'default_sender' => $item->default_sender,
            'status' => $item->status,
            'last_tested_at' => $item->last_tested_at,
            'last_credit_snapshot' => $item->last_credit_snapshot,
            'meta' => $item->meta,
        ]);

        return $this->ok(['items' => $items->all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'provider' => ['required', 'string', 'max:64'],
            'display_name' => ['required', 'string', 'max:150'],
            'base_url_override' => ['nullable', 'url'],
            'token' => ['nullable', 'string'],
            'default_sender' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', 'string', 'max:32'],
        ]);

        $item = SmsBulkProviderConnection::create([
            'tenant_id' => $this->tenantId(),
            'provider' => $data['provider'],
            'display_name' => $data['display_name'],
            'base_url_override' => $data['base_url_override'] ?? null,
            'encrypted_token' => $data['token'] ?? null,
            'default_sender' => $data['default_sender'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        return $this->ok(['id' => $item->getKey()], 201);
    }

    public function show(int $id)
    {
        $item = SmsBulkProviderConnection::query()->where('tenant_id', $this->tenantId())->findOrFail($id);

        return $this->ok([
            'id' => $item->id,
            'provider' => $item->provider,
            'display_name' => $item->display_name,
            'base_url_override' => $item->base_url_override,
            'default_sender' => $item->default_sender,
            'status' => $item->status,
            'last_tested_at' => $item->last_tested_at,
            'last_credit_snapshot' => $item->last_credit_snapshot,
            'meta' => $item->meta,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $item = SmsBulkProviderConnection::query()->where('tenant_id', $this->tenantId())->findOrFail($id);

        $data = $request->validate([
            'display_name' => ['sometimes', 'string', 'max:150'],
            'base_url_override' => ['sometimes', 'nullable', 'url'],
            'token' => ['sometimes', 'nullable', 'string'],
            'default_sender' => ['sometimes', 'nullable', 'string', 'max:32'],
            'status' => ['sometimes', 'string', 'max:32'],
        ]);

        if (array_key_exists('token', $data)) {
            $data['encrypted_token'] = $data['token'];
            unset($data['token']);
        }

        $item->update($data);

        return $this->ok(['id' => $item->getKey()]);
    }

    public function destroy(int $id)
    {
        $item = SmsBulkProviderConnection::query()->where('tenant_id', $this->tenantId())->findOrFail($id);
        $item->delete();

        return $this->ok();
    }
}
