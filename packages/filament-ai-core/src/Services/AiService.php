<?php

namespace Haida\FilamentAiCore\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\DataTransferObjects\AiResult;
use Illuminate\Contracts\Auth\Authenticatable;

class AiService
{
    public function __construct(
        protected AiProviderManager $providerManager,
        protected AiPolicyService $policyService,
        protected RedactionService $redactionService,
        protected AiRequestLogger $requestLogger,
        protected AiRateLimiter $rateLimiter,
        protected AiCircuitBreaker $circuitBreaker,
    ) {}

    /**
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function generate(
        string $module,
        string $actionType,
        string $prompt,
        array $context = [],
        array $options = [],
        ?Authenticatable $actor = null,
    ): AiResult {
        return $this->invoke('generate', $module, $actionType, ['prompt' => $prompt], $context, $options, $actor);
    }

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function summarize(
        string $module,
        string $actionType,
        string $text,
        array $schema = [],
        array $context = [],
        array $options = [],
        ?Authenticatable $actor = null,
    ): AiResult {
        return $this->invoke('summarize', $module, $actionType, ['text' => $text, 'schema' => $schema], $context, $options, $actor);
    }

    /**
     * @param  array<string, mixed>  $schema
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function extractActionItems(
        string $module,
        string $actionType,
        string $text,
        array $schema = [],
        array $context = [],
        array $options = [],
        ?Authenticatable $actor = null,
    ): AiResult {
        return $this->invoke('extractActionItems', $module, $actionType, ['text' => $text, 'schema' => $schema], $context, $options, $actor);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $taxonomy
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function classify(
        string $module,
        string $actionType,
        array $payload,
        array $taxonomy = [],
        array $context = [],
        array $options = [],
        ?Authenticatable $actor = null,
    ): AiResult {
        return $this->invoke('classify', $module, $actionType, ['payload' => $payload, 'taxonomy' => $taxonomy], $context, $options, $actor);
    }

    /**
     * @param  array<string, mixed>  $meetingContext
     * @param  array<string, mixed>  $constraints
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function generateAgenda(
        string $module,
        string $actionType,
        array $meetingContext,
        array $constraints = [],
        array $context = [],
        array $options = [],
        ?Authenticatable $actor = null,
    ): AiResult {
        return $this->invoke('generateAgenda', $module, $actionType, ['meetingContext' => $meetingContext, 'constraints' => $constraints], $context, $options, $actor);
    }

    /**
     * @param  array<string, mixed>  $transcript
     * @param  array<string, mixed>  $meetingContext
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    public function generateMinutes(
        string $module,
        string $actionType,
        array $transcript,
        array $meetingContext = [],
        array $context = [],
        array $options = [],
        ?Authenticatable $actor = null,
    ): AiResult {
        return $this->invoke('generateMinutes', $module, $actionType, ['transcript' => $transcript, 'meetingContext' => $meetingContext], $context, $options, $actor);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    protected function invoke(
        string $method,
        string $module,
        string $actionType,
        array $payload,
        array $context,
        array $options,
        ?Authenticatable $actor,
    ): AiResult {
        $policy = $this->policyService->resolvePolicy();
        $providerName = (string) ($policy['provider'] ?? config('filament-ai-core.default_provider', 'mock'));
        $tenantId = $policy['tenant_id'] ?? TenantContext::getTenantId();
        $tenantId = $tenantId ? (int) $tenantId : null;

        if (! ($policy['enabled'] ?? false)) {
            $result = AiResult::failure('disabled', 'disabled');
            $this->requestLogger->record($module, $actionType, $payload, $result, $actor, $policy['tenant_id'] ?? null);

            return $result;
        }

        $redactionPolicy = (array) ($policy['redaction_policy'] ?? []);
        $safePayload = $this->redactionService->redactInput($payload, $redactionPolicy);
        $safeContext = $this->redactionService->redactContext($context, $redactionPolicy);

        $result = null;
        if ($providerName !== 'mock' && $this->circuitBreaker->isDisabled($tenantId, $providerName)) {
            $result = AiResult::failure($providerName, 'provider_disabled', ['circuit_breaker']);
        }

        if (! $result) {
            $this->rateLimiter->throttle($tenantId, $module, $actionType);
            $result = $this->callProvider($providerName, $method, $safePayload, $safeContext, $options);

            if ($providerName !== 'mock') {
                if ($result->ok) {
                    $this->circuitBreaker->recordSuccess($tenantId, $providerName);
                } else {
                    $this->circuitBreaker->recordFailure($tenantId, $providerName, $result->error);
                }
            }
        }

        if (! $result->ok && $providerName !== 'mock') {
            $fallback = $this->callProvider('mock', $method, $safePayload, $safeContext, $options);
            $warnings = array_merge($fallback->warnings, $result->warnings, ['provider_fallback']);
            $fallback->warnings = array_values(array_unique($warnings));
            $result = $fallback;
        }

        $this->requestLogger->record($module, $actionType, (array) $safePayload, $result, $actor, $policy['tenant_id'] ?? null);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $options
     */
    protected function callProvider(
        string $providerName,
        string $method,
        array $payload,
        array $context,
        array $options,
    ): AiResult {
        if (! $this->providerManager->isEnabled($providerName) && $providerName !== 'mock') {
            return AiResult::failure($providerName, 'provider_disabled');
        }

        $provider = $this->providerManager->resolve($providerName);

        return match ($method) {
            'generate' => $provider->generate((string) ($payload['prompt'] ?? ''), $context, $options),
            'summarize' => $provider->summarize((string) ($payload['text'] ?? ''), (array) ($payload['schema'] ?? []), $context, $options),
            'extractActionItems' => $provider->extractActionItems((string) ($payload['text'] ?? ''), (array) ($payload['schema'] ?? []), $context, $options),
            'classify' => $provider->classify((array) ($payload['payload'] ?? []), (array) ($payload['taxonomy'] ?? []), $context, $options),
            'generateAgenda' => $provider->generateAgenda((array) ($payload['meetingContext'] ?? []), (array) ($payload['constraints'] ?? []), $context, $options),
            'generateMinutes' => $provider->generateMinutes((array) ($payload['transcript'] ?? []), (array) ($payload['meetingContext'] ?? []), $context, $options),
            default => AiResult::failure($providerName, 'unsupported_action'),
        };
    }
}
