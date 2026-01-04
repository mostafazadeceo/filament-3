<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapInboxResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMailtrap\Resources\MailtrapInboxResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapInbox;
use Haida\MailtrapCore\Services\MailtrapInboxService;

class EditMailtrapInbox extends EditRecord
{
    protected static string $resource = MailtrapInboxResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var MailtrapInbox $record */
        $connection = MailtrapConnection::query()->find($record->connection_id);
        if (! $connection) {
            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
            throw new \RuntimeException('Connection not found');
        }

        $payload = array_filter([
            'name' => $data['name'] ?? null,
            'status' => $data['status'] ?? null,
        ], fn ($value) => $value !== null);

        return app(MailtrapInboxService::class)->update($connection, $record, $payload);
    }
}
