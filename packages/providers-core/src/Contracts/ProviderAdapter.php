<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Contracts;

use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\DataTransferObjects\ProviderResult;

interface ProviderAdapter
{
    public function key(): string;

    public function label(): string;

    public function supportsSandbox(): bool;

    public function syncProducts(ProviderContext $context, array $payload = []): ProviderResult;

    public function syncInventory(ProviderContext $context, array $payload = []): ProviderResult;

    public function createOrder(ProviderContext $context, array $payload): ProviderResult;

    public function fulfillOrder(ProviderContext $context, array $payload): ProviderResult;

    public function fetchOrderStatus(ProviderContext $context, array $payload): ProviderResult;

    public function handleWebhook(ProviderContext $context, array $payload): ProviderResult;
}
