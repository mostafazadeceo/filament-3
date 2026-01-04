<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailMailboxResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentMailOps\Filament\Resources\MailMailboxResource;
use Haida\FilamentMailOps\Models\MailDomain;
use Haida\FilamentMailOps\Services\MailuSyncService;

class CreateMailMailbox extends CreateRecord
{
    protected static string $resource = MailMailboxResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $domain = MailDomain::query()->find($data['domain_id'] ?? null);
        if ($domain && ! empty($data['local_part'])) {
            $data['email'] = $data['local_part'].'@'.$domain->name;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! config('filament-mailops.mailu.enabled')) {
            return;
        }

        $password = $this->data['password'] ?? null;
        if (! $password) {
            return;
        }

        try {
            app(MailuSyncService::class)->syncMailbox($this->record, $password);
            Notification::make()->title('صندوق در Mailu همگام شد.')->success()->send();
        } catch (\Throwable $exception) {
            Notification::make()->title('همگام‌سازی Mailu ناموفق بود.')->danger()->send();
        }
    }
}
