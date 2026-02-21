<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services;

use InvalidArgumentException;

class MessageValidationService
{
    /**
     * @param array<string, mixed> $payload
     * @throws InvalidArgumentException
     */
    public function validate(array $payload): void
    {
        $sender = (string) ($payload['sender'] ?? '');
        if ($sender === '') {
            throw new InvalidArgumentException('Sender is required.');
        }

        $message = (string) ($payload['message'] ?? '');
        if ($message === '' && ! isset($payload['pattern_code'])) {
            throw new InvalidArgumentException('Message is required unless using a pattern.');
        }

        $forbiddenWords = (array) config('filament-sms-bulk.compliance.forbidden_words', []);
        foreach ($forbiddenWords as $word) {
            if ($word !== '' && stripos($message, (string) $word) !== false) {
                throw new InvalidArgumentException('Message contains forbidden content.');
            }
        }

        if (mb_strlen($message) > 2000) {
            throw new InvalidArgumentException('Message length exceeds max supported size.');
        }
    }
}
