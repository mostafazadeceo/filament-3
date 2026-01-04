<?php

namespace Haida\FilamentThreeCx\Contracts;

use Haida\FilamentThreeCx\Models\ThreeCxInstance;

interface ContactDirectoryInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function lookup(ThreeCxInstance $instance, ?string $phone, ?string $email): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(ThreeCxInstance $instance, string $query): array;

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function create(ThreeCxInstance $instance, array $payload): array;
}
