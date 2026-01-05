<?php

namespace Haida\FilamentNotify\Core\Support\Rules;

use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Core\Jobs\SendNotificationJob;
use Haida\FilamentNotify\Core\Models\NotificationRule;
use Haida\FilamentNotify\Core\Models\Template;
use Haida\FilamentNotify\Core\Models\Trigger;
use Haida\FilamentNotify\Core\Support\Conditions\ConditionEvaluator;
use Haida\FilamentNotify\Core\Support\Recipients\RecipientResolver;
use Haida\FilamentNotify\Core\Support\Sending\ChannelSettingsRepository;
use Illuminate\Support\Facades\Cache;

class RuleEngine
{
    public function __construct(
        protected ConditionEvaluator $conditionEvaluator,
        protected RecipientResolver $recipientResolver,
        protected ChannelRegistry $channelRegistry,
        protected ChannelSettingsRepository $channelSettingsRepository,
    ) {}

    public function dispatch(Trigger $trigger, array $context): void
    {
        $rules = NotificationRule::query()
            ->where('trigger_id', $trigger->id)
            ->where('enabled', true)
            ->get();

        foreach ($rules as $rule) {
            if (! $this->conditionEvaluator->passes($rule->conditions, $context)) {
                continue;
            }

            $recipients = $this->recipientResolver->resolve($rule->recipients ?? [], $context);
            if (empty($recipients)) {
                continue;
            }

            $channels = $rule->channels ?? [];
            foreach ($channels as $channelConfig) {
                $channelKey = $channelConfig['channel'] ?? null;
                $enabled = $channelConfig['enabled'] ?? true;

                if (! $channelKey || ! $enabled) {
                    continue;
                }

                $driver = $this->channelRegistry->get($channelKey);
                if (! $driver || ! $driver->isInstalled()) {
                    continue;
                }

                $templateId = $channelConfig['template_id'] ?? null;
                $template = $templateId ? Template::find($templateId) : null;

                $channelSettings = $this->channelSettingsRepository->getSettings($trigger->panel_id, $channelKey);

                foreach ($recipients as $recipient) {
                    if (! $this->passesThrottle($rule->throttle ?? [], $rule->id, $channelKey, $recipient)) {
                        continue;
                    }

                    SendNotificationJob::dispatch([
                        'panel_id' => $trigger->panel_id,
                        'trigger_key' => $trigger->key,
                        'rule_id' => $rule->id,
                        'channel' => $channelKey,
                        'recipient' => $recipient,
                        'context' => $context,
                        'channel_settings' => $channelSettings,
                        'template_id' => $template?->id,
                    ])->onConnection(config('filament-notify.queue.connection'))
                        ->onQueue(config('filament-notify.queue.queue'));
                }
            }
        }
    }

    /**
     * @param  array<string, mixed>  $throttle
     * @param  array<string, mixed>  $recipient
     */
    protected function passesThrottle(array $throttle, int $ruleId, string $channelKey, array $recipient): bool
    {
        $limit = $throttle['limit'] ?? null;
        $seconds = $throttle['seconds'] ?? null;

        if (! $limit || ! $seconds) {
            return true;
        }

        $recipientKey = $recipient['email'] ?? $recipient['phone'] ?? $recipient['telegram_chat_id'] ?? $recipient['whatsapp_number'] ?? $recipient['bale_chat_id'] ?? 'unknown';
        $cacheKey = sprintf('fn:throttle:%s:%s:%s', $ruleId, $channelKey, $recipientKey);

        if (! Cache::has($cacheKey)) {
            Cache::put($cacheKey, 1, now()->addSeconds((int) $seconds));

            return true;
        }

        $count = Cache::increment($cacheKey);

        return $count <= (int) $limit;
    }
}
