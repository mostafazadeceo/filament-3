<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Haida\MailtrapCore\Http\Requests\StoreConnectionRequest;
use Haida\MailtrapCore\Http\Requests\UpdateConnectionRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapConnectionResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapConnectionService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ConnectionController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapConnection::class, 'connection');
    }

    public function index(): AnonymousResourceCollection
    {
        $connections = MailtrapConnection::query()->latest()->paginate();

        return MailtrapConnectionResource::collection($connections);
    }

    public function show(MailtrapConnection $connection): MailtrapConnectionResource
    {
        return new MailtrapConnectionResource($connection);
    }

    public function store(StoreConnectionRequest $request, MailtrapConnectionService $service): MailtrapConnectionResource
    {
        $data = $request->validated();
        $data['created_by_user_id'] = auth()->id();
        $data['updated_by_user_id'] = auth()->id();

        $connection = MailtrapConnection::query()->create($data);

        if ($request->boolean('test_connection')) {
            $service->testConnection($connection);
            $connection->refresh();
        }

        return new MailtrapConnectionResource($connection);
    }

    public function update(UpdateConnectionRequest $request, MailtrapConnection $connection, MailtrapConnectionService $service): MailtrapConnectionResource
    {
        $data = $request->validated();
        $data['updated_by_user_id'] = auth()->id();

        if (array_key_exists('api_token', $data) && blank($data['api_token'])) {
            unset($data['api_token']);
        }

        if (array_key_exists('send_api_token', $data) && blank($data['send_api_token'])) {
            unset($data['send_api_token']);
        }

        $connection->update($data);

        if ($request->boolean('test_connection')) {
            $service->testConnection($connection);
            $connection->refresh();
        }

        return new MailtrapConnectionResource($connection->refresh());
    }

    public function destroy(MailtrapConnection $connection): array
    {
        $connection->delete();

        return ['status' => 'ok'];
    }
}
