<?php

namespace Haida\FilamentCryptoGateway\Contracts;

interface AiInsightProvider
{
    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    public function generateInsights(array $context): array;
}
