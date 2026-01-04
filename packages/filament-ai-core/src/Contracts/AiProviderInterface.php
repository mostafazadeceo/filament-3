<?php

namespace Haida\FilamentAiCore\Contracts;

use Haida\FilamentAiCore\DataTransferObjects\AiResult;

interface AiProviderInterface
{
    /**
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function generate(string $prompt, array $context = [], array $options = []): AiResult;

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function summarize(string $text, array $schema = [], array $context = [], array $options = []): AiResult;

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function extractActionItems(string $text, array $schema = [], array $context = [], array $options = []): AiResult;

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $taxonomy
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function classify(array $payload, array $taxonomy = [], array $context = [], array $options = []): AiResult;

    /**
     * @param  array<string, mixed>  $meetingContext
     * @param  array<string, mixed>  $constraints
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function generateAgenda(array $meetingContext, array $constraints = [], array $context = [], array $options = []): AiResult;

    /**
     * @param  array<string, mixed>  $transcript
     * @param  array<string, mixed>  $meetingContext
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function generateMinutes(array $transcript, array $meetingContext = [], array $context = [], array $options = []): AiResult;
}
