<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources\MailMailboxResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Haida\FilamentMailOps\Filament\Resources\MailMailboxResource;
use Haida\FilamentMailOps\Models\MailDomain;

class EditMailMailbox extends EditRecord
{
    protected static string $resource = MailMailboxResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $domain = MailDomain::query()->find($data['domain_id'] ?? null);
        if ($domain && ! empty($data['local_part'])) {
            $data['email'] = $data['local_part'].'@'.$domain->name;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }
}
