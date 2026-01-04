<?php

namespace Haida\FilamentThreeCx\Contracts;

interface ThreeCxClientInterface
{
    /**
     * @param  array<string, mixed>  $query
     * @param  array<string, mixed>|null  $body
     * @return array<string, mixed>
     */
    public function request(string $method, string $path, array $query = [], ?array $body = null): array;
}
