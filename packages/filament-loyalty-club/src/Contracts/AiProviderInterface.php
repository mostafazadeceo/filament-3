<?php

namespace Haida\FilamentLoyaltyClub\Contracts;

interface AiProviderInterface
{
    /**
     * @param  array<string, mixed>  $segmentContext
     * @return array<int, array<string, mixed>>
     */
    public function recommendOffers(array $segmentContext): array;

    /**
     * @param  array<string, mixed>  $customerContext
     * @return array<string, mixed>
     */
    public function detectChurnRisk(array $customerContext): array;

    /**
     * @param  array<string, mixed>  $campaignContext
     * @return array<int, array<string, mixed>>
     */
    public function draftCampaignCopy(array $campaignContext): array;

    /**
     * @param  array<string, mixed>  $signalContext
     */
    public function explainFraudSignal(array $signalContext): string;
}
