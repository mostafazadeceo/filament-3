<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Controllers\Api\V1;

use Haida\MailtrapCore\Http\Requests\StoreSingleSendRequest;
use Haida\MailtrapCore\Http\Resources\MailtrapSingleSendResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapSingleSend;
use Haida\MailtrapCore\Services\MailtrapSingleSendService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SingleSendController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(MailtrapSingleSend::class, 'single_send');
    }

    public function index(): AnonymousResourceCollection
    {
        $sends = MailtrapSingleSend::query()->latest()->paginate();

        return MailtrapSingleSendResource::collection($sends);
    }

    public function show(MailtrapSingleSend $singleSend): MailtrapSingleSendResource
    {
        return new MailtrapSingleSendResource($singleSend);
    }

    public function store(StoreSingleSendRequest $request, MailtrapSingleSendService $service): MailtrapSingleSendResource
    {
        $data = $request->validated();
        $connection = MailtrapConnection::query()
            ->where('tenant_id', $data['tenant_id'])
            ->where('id', $data['connection_id'])
            ->firstOrFail();

        $options = array_filter([
            'to_name' => $data['to_name'] ?? null,
            'from_email' => $data['from_email'] ?? null,
            'from_name' => $data['from_name'] ?? null,
            'text' => $data['text_body'] ?? null,
            'html' => $data['html_body'] ?? null,
            'created_by_user_id' => auth()->id(),
        ], fn ($value) => $value !== null && $value !== '');

        $record = $service->sendAndLog(
            $connection,
            (string) $data['to_email'],
            (string) $data['subject'],
            (string) ($data['text_body'] ?? ''),
            $options
        );

        return new MailtrapSingleSendResource($record);
    }
}
