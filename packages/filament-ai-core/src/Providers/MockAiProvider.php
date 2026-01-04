<?php

namespace Haida\FilamentAiCore\Providers;

use Haida\FilamentAiCore\Contracts\AiProviderInterface;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;

class MockAiProvider implements AiProviderInterface
{
    protected string $providerName = 'mock';

    public function generate(string $prompt, array $context = [], array $options = []): AiResult
    {
        $hash = $this->hashPayload([$prompt, $context, $options]);

        return AiResult::success($this->providerName, $this->model(), "mock:generate:{$hash}");
    }

    public function summarize(string $text, array $schema = [], array $context = [], array $options = []): AiResult
    {
        $hash = $this->hashPayload([$text, $schema, $context, $options]);
        $json = $this->schemaOutput($schema, $hash, [
            'summary' => "mock summary {$hash}",
        ]);

        return AiResult::success($this->providerName, $this->model(), "mock:summarize:{$hash}", $json);
    }

    public function extractActionItems(string $text, array $schema = [], array $context = [], array $options = []): AiResult
    {
        $hash = $this->hashPayload([$text, $schema, $context, $options]);
        $json = $this->schemaOutput($schema, $hash, [
            'items' => ["mock action item {$hash}"],
        ]);

        return AiResult::success($this->providerName, $this->model(), "mock:action-items:{$hash}", $json);
    }

    public function classify(array $payload, array $taxonomy = [], array $context = [], array $options = []): AiResult
    {
        $hash = $this->hashPayload([$payload, $taxonomy, $context, $options]);
        $json = $this->schemaOutput($taxonomy, $hash, [
            'label' => 'mock',
            'score' => 0.5,
        ]);

        return AiResult::success($this->providerName, $this->model(), "mock:classify:{$hash}", $json);
    }

    public function generateAgenda(array $meetingContext, array $constraints = [], array $context = [], array $options = []): AiResult
    {
        $hash = $this->hashPayload([$meetingContext, $constraints, $context, $options]);
        $json = [
            'agenda' => [
                ['title' => 'Mock agenda item 1', 'timebox_minutes' => 10],
                ['title' => 'Mock agenda item 2', 'timebox_minutes' => 15],
            ],
        ];

        return AiResult::success($this->providerName, $this->model(), "mock:agenda:{$hash}", $json);
    }

    public function generateMinutes(array $transcript, array $meetingContext = [], array $context = [], array $options = []): AiResult
    {
        $hash = $this->hashPayload([$transcript, $meetingContext, $context, $options]);
        $json = [
            'overview' => "mock minutes overview {$hash}",
            'decisions' => ["mock decision {$hash}"],
            'action_items' => [
                ['title' => "mock action {$hash}", 'owner' => null],
            ],
        ];

        return AiResult::success($this->providerName, $this->model(), "mock:minutes:{$hash}", $json);
    }

    protected function model(): string
    {
        return (string) config('filament-ai-core.providers.mock.model', 'mock-v1');
    }

    /**
     * @param  array<int, mixed>  $payload
     */
    protected function hashPayload(array $payload): string
    {
        $normalized = $this->normalize($payload);
        $json = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        return substr(sha1($json ?: ''), 0, 12);
    }

    protected function normalize(mixed $value): mixed
    {
        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalize($item);
            }
            if ($this->isAssoc($normalized)) {
                ksort($normalized);
            }

            return $normalized;
        }

        return $value;
    }

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $fallback
     * @return array<string, mixed>
     */
    protected function schemaOutput(array $schema, string $seed, array $fallback = []): array
    {
        if ($schema === []) {
            return $fallback;
        }

        if (isset($schema['type'])) {
            $value = $this->valueForSchema($schema, $seed);

            return is_array($value) ? $value : ['value' => $value];
        }

        if ($this->isAssoc($schema)) {
            $output = [];
            foreach ($schema as $key => $sub) {
                $output[$key] = $this->valueForSchema(is_array($sub) ? $sub : ['type' => 'string'], $seed.$key);
            }

            return $output;
        }

        return $fallback;
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    protected function valueForSchema(array $schema, string $seed): mixed
    {
        $type = $schema['type'] ?? null;

        if ($type === 'object') {
            $properties = (array) ($schema['properties'] ?? []);
            $output = [];
            foreach ($properties as $key => $propSchema) {
                $output[$key] = $this->valueForSchema((array) $propSchema, $seed.$key);
            }

            return $output;
        }

        if ($type === 'array') {
            $items = (array) ($schema['items'] ?? ['type' => 'string']);

            return [$this->valueForSchema($items, $seed.'0')];
        }

        if ($type === 'integer') {
            return abs(crc32($seed)) % 100;
        }

        if ($type === 'number') {
            return round((abs(crc32($seed)) % 10000) / 100, 2);
        }

        if ($type === 'boolean') {
            return (crc32($seed) % 2) === 0;
        }

        return 'mock_'.$seed;
    }

    protected function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }
}
