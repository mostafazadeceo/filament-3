<?php

namespace Haida\FilamentNotify\Core\Jobs;

use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Core\Models\DeliveryLog;
use Haida\FilamentNotify\Core\Models\NotificationRule;
use Haida\FilamentNotify\Core\Models\Template;
use Haida\FilamentNotify\Core\Support\Context\DeliveryContext;
use Haida\FilamentNotify\Core\Support\Rendering\TemplateRenderer;
use Haida\FilamentNotify\Core\Support\Sending\DeliveryResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public array $payload,
    ) {}

    public function handle(ChannelRegistry $registry, TemplateRenderer $renderer): void
    {
        $rule = NotificationRule::find($this->payload['rule_id']);
        if (! $rule) {
            return;
        }

        $channelKey = (string) ($this->payload['channel'] ?? '');
        $driver = $registry->get($channelKey);
        if (! $driver || ! $driver->isInstalled()) {
            $this->logFailure('channel_not_available');
            return;
        }

        $template = null;
        $templateId = $this->payload['template_id'] ?? null;
        if ($templateId) {
            $template = Template::find($templateId);
        }

        if ($driver->supportsTemplates() && ! $template) {
            $this->logFailure('missing_template');
            return;
        }

        $context = $this->payload['context'] ?? [];
        $rendered = $renderer->render(
            $template?->subject,
            $template?->body ?? '',
            $context,
            $template?->meta ?? [],
        );

        $deliveryContext = new DeliveryContext(
            panelId: (string) $this->payload['panel_id'],
            triggerKey: (string) $this->payload['trigger_key'],
            rule: $rule,
            channelKey: $channelKey,
            channelSettings: $this->payload['channel_settings'] ?? [],
            recipient: $this->payload['recipient'] ?? [],
            context: $context,
            template: $template,
        );

        try {
            $result = $driver->send($deliveryContext, $rendered);
        } catch (\Throwable $exception) {
            $result = DeliveryResult::failure($exception->getMessage());
        }

        if ($result->success) {
            DeliveryLog::create([
                'panel_id' => $deliveryContext->panelId,
                'rule_id' => $rule->id,
                'trigger_key' => $deliveryContext->triggerKey,
                'channel' => $channelKey,
                'recipient' => $this->resolveRecipientLabel($deliveryContext->recipient),
                'status' => 'sent',
                'request_payload' => $this->payload,
                'response_payload' => $result->response,
                'error' => null,
            ]);
        } else {
            DeliveryLog::create([
                'panel_id' => $deliveryContext->panelId,
                'rule_id' => $rule->id,
                'trigger_key' => $deliveryContext->triggerKey,
                'channel' => $channelKey,
                'recipient' => $this->resolveRecipientLabel($deliveryContext->recipient),
                'status' => 'failed',
                'request_payload' => $this->payload,
                'response_payload' => $result->response,
                'error' => $result->error,
            ]);
        }
    }

    protected function logFailure(string $message): void
    {
        DeliveryLog::create([
            'panel_id' => (string) ($this->payload['panel_id'] ?? 'unknown'),
            'rule_id' => $this->payload['rule_id'] ?? null,
            'trigger_key' => (string) ($this->payload['trigger_key'] ?? 'unknown'),
            'channel' => (string) ($this->payload['channel'] ?? 'unknown'),
            'recipient' => $this->resolveRecipientLabel($this->payload['recipient'] ?? []),
            'status' => 'failed',
            'request_payload' => $this->payload,
            'response_payload' => null,
            'error' => $message,
        ]);
    }

    /**
     * @param  array<string, mixed>  $recipient
     */
    protected function resolveRecipientLabel(array $recipient): string
    {
        return (string) ($recipient['email'] ?? $recipient['phone'] ?? $recipient['telegram_chat_id'] ?? $recipient['whatsapp_number'] ?? $recipient['bale_chat_id'] ?? 'unknown');
    }
}
