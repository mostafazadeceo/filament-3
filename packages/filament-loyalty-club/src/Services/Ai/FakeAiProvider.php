<?php

namespace Haida\FilamentLoyaltyClub\Services\Ai;

use Haida\FilamentLoyaltyClub\Contracts\AiProviderInterface;

class FakeAiProvider implements AiProviderInterface
{
    public function recommendOffers(array $segmentContext): array
    {
        return [
            [
                'title' => 'پیشنهاد آزمایشی',
                'description' => 'این پیشنهاد برای تست فعال است.',
                'score' => 0.5,
            ],
        ];
    }

    public function detectChurnRisk(array $customerContext): array
    {
        return [
            'score' => 0.1,
            'label' => 'کم',
            'reasons' => ['ارزیابی نمونه برای محیط تست'],
        ];
    }

    public function draftCampaignCopy(array $campaignContext): array
    {
        return [
            [
                'headline' => 'پیشنهاد ویژه باشگاه مشتریان',
                'body' => 'با امتیازهای جدید، جایزه بعدی شما نزدیک‌تر است.',
            ],
        ];
    }

    public function explainFraudSignal(array $signalContext): string
    {
        return 'بررسی نمونه: برای توضیح دقیق‌تر داده بیشتری نیاز است.';
    }
}
