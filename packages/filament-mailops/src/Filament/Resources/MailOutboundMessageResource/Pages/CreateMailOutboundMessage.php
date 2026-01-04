<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailOps\Filament\Resources\MailOutboundMessageResource;
use Haida\FilamentMailOps\Models\MailMailbox;
use Haida\FilamentMailOps\Services\MailSender;

class CreateMailOutboundMessage extends CreateRecord
{
    protected static string $resource = MailOutboundMessageResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $mailbox = MailMailbox::query()->find($data['mailbox_id'] ?? null);
        if (! $mailbox) {
            Notification::make()->title('صندوق ارسال یافت نشد.')->danger()->send();
            throw new \RuntimeException('Mailbox not found');
        }

        if (empty($data['text_body']) && empty($data['html_body'])) {
            Notification::make()->title('متن ایمیل الزامی است.')->danger()->send();
            throw new \RuntimeException('Body is required');
        }

        $record = app(MailSender::class)->sendAndLog($mailbox, $data);

        if ($record->status === 'sent') {
            Notification::make()->title('ایمیل ارسال شد.')->success()->send();
        } else {
            Notification::make()->title('ارسال ایمیل ناموفق بود.')->danger()->send();
        }

        return $record;
    }
}
