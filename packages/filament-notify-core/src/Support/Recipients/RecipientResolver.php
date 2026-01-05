<?php

namespace Haida\FilamentNotify\Core\Support\Recipients;

use Illuminate\Database\Eloquent\Model;

class RecipientResolver
{
    /**
     * @param  array<int, array<string, mixed>>  $recipientRules
     * @return array<int, array<string, mixed>>
     */
    public function resolve(array $recipientRules, array $context): array
    {
        $recipients = [];

        foreach ($recipientRules as $rule) {
            $type = $rule['type'] ?? null;

            if ($type === 'initiator') {
                $user = $context['user'] ?? null;
                if ($user instanceof Model) {
                    $this->pushRecipient($recipients, $this->fromModel($user));
                }

                continue;
            }

            if ($type === 'emails') {
                $emails = $this->splitList($rule['emails'] ?? '');
                foreach ($emails as $email) {
                    $this->pushRecipient($recipients, new Recipient(email: $email));
                }

                continue;
            }

            if ($type === 'phones') {
                $phones = $this->splitList($rule['phones'] ?? '');
                foreach ($phones as $phone) {
                    $this->pushRecipient($recipients, new Recipient(phone: $phone));
                }

                continue;
            }

            if ($type === 'relation') {
                $path = $rule['path'] ?? null;
                if (! $path) {
                    continue;
                }

                $value = data_get($context, $path);
                if ($value instanceof Model) {
                    $this->pushRecipient($recipients, $this->fromModel($value));
                } elseif ($value instanceof \Illuminate\Support\Collection) {
                    foreach ($value as $item) {
                        if ($item instanceof Model) {
                            $this->pushRecipient($recipients, $this->fromModel($item));
                        }
                    }
                } elseif (is_array($value)) {
                    foreach ($value as $item) {
                        if ($item instanceof Model) {
                            $this->pushRecipient($recipients, $this->fromModel($item));
                        }
                    }
                }

                continue;
            }

            if ($type === 'role') {
                $roleName = $rule['role'] ?? null;
                if (! $roleName) {
                    continue;
                }

                $userModel = config('auth.providers.users.model');
                if (! $userModel || ! class_exists($userModel)) {
                    continue;
                }

                try {
                    $users = $userModel::role($roleName)->get();
                } catch (\Throwable) {
                    $users = collect();
                }

                foreach ($users as $user) {
                    if ($user instanceof Model) {
                        $this->pushRecipient($recipients, $this->fromModel($user));
                    }
                }
            }
        }

        return array_values($recipients);
    }

    protected function fromModel(Model $model): Recipient
    {
        $email = $model->getAttribute('email') ?? null;
        $phone = $model->getAttribute('phone') ?? $model->getAttribute('mobile') ?? null;
        $telegram = $model->getAttribute('telegram_chat_id') ?? null;
        $whatsapp = $model->getAttribute('whatsapp_number') ?? null;
        $bale = $model->getAttribute('bale_chat_id') ?? null;

        return new Recipient(
            notifiable: $model,
            email: $email,
            phone: $phone,
            telegramChatId: $telegram,
            whatsappNumber: $whatsapp,
            baleChatId: $bale,
        );
    }

    protected function splitList(string $value): array
    {
        $items = preg_split('/[\n,]+/', $value) ?: [];

        return array_values(array_filter(array_map('trim', $items)));
    }

    /**
     * @param  array<int, array<string, mixed>>  $recipients
     */
    protected function pushRecipient(array &$recipients, Recipient $recipient): void
    {
        $key = $recipient->email ?: ($recipient->phone ?: ($recipient->telegramChatId ?: ($recipient->whatsappNumber ?: $recipient->baleChatId)));
        if (! $key && $recipient->notifiable) {
            $key = $recipient->notifiable::class.':'.$recipient->notifiable->getKey();
        }

        if (! $key) {
            return;
        }

        $recipients[$key] = $recipient->toArray();
    }
}
