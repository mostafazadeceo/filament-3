<?php

declare(strict_types=1);

namespace Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailtrap\Resources\MailtrapSingleSendResource;
use Haida\MailtrapCore\Models\MailtrapConnection;
use Haida\MailtrapCore\Services\MailtrapSingleSendService;

class CreateMailtrapSingleSend extends CreateRecord
{
    protected static string $resource = MailtrapSingleSendResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $connection = MailtrapConnection::query()->find($data['connection_id']);
        if (! $connection) {
            Notification::make()->title('اتصال یافت نشد.')->danger()->send();
            throw new \RuntimeException('Connection not found');
        }

        $options = array_filter([
            'to_name' => $data['to_name'] ?? null,
            'from_email' => $data['from_email'] ?? null,
            'from_name' => $data['from_name'] ?? null,
            'text' => $data['text_body'] ?? null,
            'html' => $data['html_body'] ?? null,
            'sandbox_inbox_id' => $data['sandbox_inbox_id'] ?? null,
            'created_by_user_id' => auth()->id(),
        ], fn ($value) => $value !== null && $value !== '');

        $record = app(MailtrapSingleSendService::class)->sendAndLog(
            $connection,
            (string) $data['to_email'],
            (string) $data['subject'],
            (string) ($data['text_body'] ?? ''),
            $options,
        );

        if ($record->status === 'sent') {
            Notification::make()->title('ایمیل ارسال شد.')->success()->send();
        } else {
            Notification::make()->title('ارسال ایمیل ناموفق بود.')->danger()->send();
        }

        return $record;
    }
}
