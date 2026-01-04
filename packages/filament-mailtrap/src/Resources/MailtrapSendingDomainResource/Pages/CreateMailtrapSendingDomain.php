<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapDomainService;

class CreateMailtrapSendingDomain extends CreateRecord
{
    protected static string $resource = MailtrapSendingDomainResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $connection = MailtrapConnection::query()->find($data['connection_id']);
        if (! $connection) {
            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
            throw new \RuntimeException('Connection not found');
        }

        $payload = array_filter([
            'domain_name' => $data['domain_name'] ?? null,
        ], fn ($value) => $value !== null);

        return app(MailtrapDomainService::class)->create($connection, $payload);
    }
}
