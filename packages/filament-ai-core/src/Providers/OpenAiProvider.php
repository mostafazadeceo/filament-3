<?php

namespace Haida\FilamentAiCore\Providers;

use Haida\FilamentAiCore\Contracts\AiProviderInterface;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;

class OpenAiProvider implements AiProviderInterface
{
    public function generate(string $prompt, array $context = [], array $options = []): AiResult
    {
        return $this->disabledResult();
    }

    public function summarize(string $text, array $schema = [], array $context = [], array $options = []): AiResult
    {
        return $this->disabledResult();
    }

    public function extractActionItems(string $text, array $schema = [], array $context = [], array $options = []): AiResult
    {
        return $this->disabledResult();
    }

    public function classify(array $payload, array $taxonomy = [], array $context = [], array $options = []): AiResult
    {
        return $this->disabledResult();
    }

    public function generateAgenda(array $meetingContext, array $constraints = [], array $context = [], array $options = []): AiResult
    {
        return $this->disabledResult();
    }

    public function generateMinutes(array $transcript, array $meetingContext = [], array $context = [], array $options = []): AiResult
    {
        return $this->disabledResult();
    }

    protected function disabledResult(): AiResult
    {
        if (! config('filament-ai-core.providers.openai.enabled', false)) {
            return AiResult::failure('openai', 'openai_disabled');
        }

        if (! config('filament-ai-core.providers.openai.api_key')) {
            return AiResult::failure('openai', 'openai_missing_api_key');
        }

        return AiResult::failure('openai', 'openai_not_implemented');
    }
}
