<?php

namespace Haida\FilamentAiCore\Services;

class RedactionService
{
    /**
     * @param  array<string, mixed>  $policy
     */
    public function redactInput(string|array $input, array $policy): string|array
    {
        if (is_array($input)) {
            return $this->redactArray($input, $policy);
        }

        return $this->redactString($input, $policy);
    }

    /**
     * @param  array<string, mixed>  $context
     * @param  array<string, mixed>  $policy
     * @return array<string, mixed>
     */
    public function redactContext(array $context, array $policy): array
    {
        return $this->redactArray($context, $policy);
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $policy
     * @return array<string, mixed>
     */
    protected function redactArray(array $data, array $policy): array
    {
        $result = [];

        foreach ($data as $key => $value) {
            $keyString = is_string($key) ? strtolower($key) : null;

            if ($keyString && $this->shouldRemoveKey($keyString, $policy)) {
                continue;
            }

            if (is_array($value)) {
                $result[$key] = $this->redactArray($value, $policy);

                continue;
            }

            if (is_string($value)) {
                $result[$key] = $this->redactString($value, $policy);

                continue;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $policy
     */
    protected function redactString(string $value, array $policy): string
    {
        $clean = $value;

        $emailPolicy = $policy['emails'] ?? 'mask';
        if ($emailPolicy !== 'keep') {
            $clean = preg_replace_callback('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', function (array $matches) use ($emailPolicy) {
                $email = $matches[0] ?? '';
                if ($emailPolicy === 'remove') {
                    return '***';
                }

                return $this->maskEmail($email);
            }, $clean) ?? $clean;
        }

        $phonePolicy = $policy['phones'] ?? 'mask';
        if ($phonePolicy !== 'keep') {
            $clean = preg_replace_callback('/(?:\+?\d[\d\s\-()]{7,}\d)/', function (array $matches) use ($phonePolicy) {
                $phone = $matches[0] ?? '';
                if ($phonePolicy === 'remove') {
                    return '***';
                }

                return $this->maskPhone($phone);
            }, $clean) ?? $clean;
        }

        $terms = $policy['sensitive_terms'] ?? [];
        if (is_array($terms) && $terms !== []) {
            foreach ($terms as $term) {
                if (! is_string($term) || $term === '') {
                    continue;
                }
                $clean = str_ireplace($term, '***', $clean);
            }
        }

        return $clean;
    }

    /**
     * @param  array<string, mixed>  $policy
     */
    protected function shouldRemoveKey(string $key, array $policy): bool
    {
        if (in_array($key, ['ip', 'ip_address'], true)) {
            return ($policy['ip'] ?? null) === 'remove';
        }

        if (in_array($key, ['ua', 'user_agent'], true)) {
            return ($policy['ua'] ?? null) === 'remove';
        }

        return false;
    }

    protected function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return '***';
        }

        [$name, $domain] = explode('@', $email, 2);
        if ($name === '') {
            return '***@'.$domain;
        }

        return substr($name, 0, 1).'***@'.$domain;
    }

    protected function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '***';
        }

        $keep = substr($digits, -2);
        $masked = str_repeat('*', max(strlen($digits) - 2, 0)).$keep;

        return $masked;
    }
}
