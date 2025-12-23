<?php

namespace Haida\FilamentNotify\Core\Support\Recipients;

use Illuminate\Database\Eloquent\Model;

class Recipient
{
    public function __construct(
        public ?Model $notifiable = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $telegramChatId = null,
        public ?string $whatsappNumber = null,
        public ?string $baleChatId = null,
    ) {}

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'phone' => $this->phone,
            'telegram_chat_id' => $this->telegramChatId,
            'whatsapp_number' => $this->whatsappNumber,
            'bale_chat_id' => $this->baleChatId,
            'notifiable' => $this->notifiable,
        ];
    }
}
