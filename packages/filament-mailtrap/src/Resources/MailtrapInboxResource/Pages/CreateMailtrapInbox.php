<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapInboxResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailtrap\Resources\MailtrapInboxResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapInboxService;

class CreateMailtrapInbox extends CreateRecord
{
    protected static string $resource = MailtrapInboxResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $connection = MailtrapConnection::query()->find($data['connection_id']);
        if (! $connection) {
            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
            throw new \RuntimeException('Connection not found');
        }

        $payload = array_filter([
            'name' => $data['name'] ?? null,
            'status' => $data['status'] ?? null,
            'project_id' => $data['project_id'] ?? null,
        ], fn ($value) => $value !== null);

        return app(MailtrapInboxService::class)->create($connection, $payload);
    }
}
