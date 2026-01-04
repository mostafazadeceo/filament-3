<?php

namespace Haida\ThemeEngine;

class ThemeDefinition
{
    /**
     * @param array<string, string> $tokens
     * @param array<string, string> $assets
     */
    public function __construct(
        public string $key,
        public string $name,
        public string $version,
        public ?string $description = null,
        public array $tokens = [],
        public array $assets = [],
        public ?string $view = null,
        public ?string $createdAtJalali = null,
    ) {
    }

    /**
     * @param array<string, mixed> $payload
     */
    public static function fromArray(string $key, array $payload): self
    {
        return new self(
            key: $key,
            name: (string) ($payload['name'] ?? $key),
            version: (string) ($payload['version'] ?? '1.0.0'),
            description: isset($payload['description']) ? (string) $payload['description'] : null,
            tokens: is_array($payload['tokens'] ?? null) ? $payload['tokens'] : [],
            assets: is_array($payload['assets'] ?? null) ? $payload['assets'] : [],
            view: isset($payload['view']) ? (string) $payload['view'] : null,
            createdAtJalali: isset($payload['created_at_jalali']) ? (string) $payload['created_at_jalali'] : null,
        );
    }
}
