<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMailtrap\Resources\MailtrapSendingDomainResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Models\MailtrapSendingDomain;
use Haida\MailtrapCore\Services\MailtrapDomainService;

class EditMailtrapSendingDomain extends EditRecord
{
    protected static string $resource = MailtrapSendingDomainResource::class;

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        /** @var MailtrapSendingDomain $record */
        $connection = MailtrapConnection::query()->find($record->connection_id);
        if (! $connection) {
            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
            throw new \RuntimeException('Connection not found');
        }

        $payload = array_filter([
            'domain_name' => $data['domain_name'] ?? null,
        ], fn ($value) => $value !== null);

        return app(MailtrapDomainService::class)->update($connection, $record, $payload);
    }
}
