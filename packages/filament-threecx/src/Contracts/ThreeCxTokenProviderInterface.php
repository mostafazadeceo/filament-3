<?php

namespace Haida\FilamentThreeCx\Contracts;

use DateTimeInterface;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;

interface ThreeCxTokenProviderInterface
{
    /**
     * @return array{access_token:string, expires_at:DateTimeInterface}|null
     */
    public function getToken(ThreeCxInstance $instance, string $scope): ?array;

    public function storeToken(ThreeCxInstance $instance, string $scope, string $token, DateTimeInterface $expiresAt): void;

    public function forgetToken(ThreeCxInstance $instance, string $scope): void;
}
