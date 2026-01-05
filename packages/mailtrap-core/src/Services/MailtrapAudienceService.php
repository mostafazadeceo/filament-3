<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Services;

use Haida\MailtrapCore\Models\MailtrapAudience;
use Haida\MailtrapCore\Models\MailtrapAudienceContact;

class MailtrapAudienceService
{
    /**
     * @param  array<int, array<string, mixed>>  $contacts
     */
    public function upsertContacts(MailtrapAudience $audience, array $contacts): int
    {
        $count = 0;

        foreach ($contacts as $contact) {
            $email = (string) ($contact['email'] ?? '');
            if ($email === '') {
                continue;
            }

            MailtrapAudienceContact::query()->updateOrCreate([
                'tenant_id' => $audience->tenant_id,
                'audience_id' => $audience->getKey(),
                'email' => $email,
            ], [
                'name' => $contact['name'] ?? null,
                'status' => $contact['status'] ?? 'subscribed',
                'metadata' => $contact['metadata'] ?? null,
            ]);
            $count++;
        }

        return $count;
    }
}
