<?php

namespace Haida\FilamentAiCore\DataTransferObjects;

final class AiResult
{
    /**
     * @param  array<int, string>  $warnings
     */
    public function __construct(
        public bool $ok,
        public string $provider,
        public ?string $model = null,
        public ?string $output_text = null,
        public mixed $output_json = null,
        public ?int $tokens = null,
        public ?int $latency_ms = null,
        public array $warnings = [],
        public ?string $error = null,
    ) {}

    public static function success(
        string $provider,
        ?string $model = null,
        ?string $outputText = null,
        mixed $outputJson = null,
        ?int $tokens = null,
        ?int $latencyMs = null,
        array $warnings = [],
    ): self {
        return new self(true, $provider, $model, $outputText, $outputJson, $tokens, $latencyMs, $warnings, null);
    }

    public static function failure(string $provider, string $error, array $warnings = [], ?int $latencyMs = null): self
    {
        return new self(false, $provider, null, null, null, null, $latencyMs, $warnings, $error);
    }
}
