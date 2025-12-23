<?php

namespace Haida\FilamentNotify\Core\Support\Rendering;

class TemplateRenderer
{
    public function render(?string $subject, string $body, array $context, array $meta = []): RenderedMessage
    {
        return new RenderedMessage(
            $subject ? $this->renderString($subject, $context) : null,
            $this->renderString($body, $context),
            $meta,
        );
    }

    public function renderString(string $template, array $context): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/', function (array $matches) use ($context): string {
            $key = $matches[1] ?? '';
            if ($key === '') {
                return '';
            }

            $value = data_get($context, $key);

            if (is_bool($value)) {
                return $value ? 'true' : 'false';
            }

            if (is_scalar($value)) {
                return (string) $value;
            }

            if (is_array($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
            }

            if (is_object($value)) {
                if (method_exists($value, '__toString')) {
                    return (string) $value;
                }

                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '';
            }

            return '';
        }, $template) ?? $template;
    }
}
