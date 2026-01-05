<?php

namespace Haida\PlatformCore\Support;

use InvalidArgumentException;

class PluginManifest
{
    public function __construct(
        public readonly string $nameFa,
        public readonly ?string $descriptionFa,
        public readonly string $version,
        public readonly string $createdAtJalali,
        public readonly array $meta = [],
    ) {}

    public static function fromArray(array $data): self
    {
        $nameFa = $data['name_fa'] ?? null;
        $version = $data['version'] ?? null;
        $createdAtJalali = $data['created_at_jalali'] ?? null;

        if (! is_string($nameFa) || $nameFa === '') {
            throw new InvalidArgumentException('Manifest name_fa is required.');
        }

        if (! is_string($version) || $version === '') {
            throw new InvalidArgumentException('Manifest version is required.');
        }

        if (! is_string($createdAtJalali) || $createdAtJalali === '') {
            throw new InvalidArgumentException('Manifest created_at_jalali is required.');
        }

        return new self(
            $nameFa,
            is_string($data['description_fa'] ?? null) ? $data['description_fa'] : null,
            $version,
            $createdAtJalali,
            is_array($data['meta'] ?? null) ? $data['meta'] : [],
        );
    }

    public function toArray(): array
    {
        return [
            'name_fa' => $this->nameFa,
            'description_fa' => $this->descriptionFa,
            'version' => $this->version,
            'created_at_jalali' => $this->createdAtJalali,
            'meta' => $this->meta,
        ];
    }
}
