<?php

namespace Haida\FilamentWorkhub\Services;

use Haida\FilamentWorkhub\Models\CustomField;
use Haida\FilamentWorkhub\Models\CustomFieldValue;
use Haida\FilamentWorkhub\Models\WorkItem;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CustomFieldManager
{
    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    public function validateValues(string $scope, array $values, int $tenantId, bool $requireAll = false): array
    {
        $fields = CustomField::query()
            ->where('tenant_id', $tenantId)
            ->where('scope', $scope)
            ->where('is_active', true)
            ->get()
            ->keyBy('key');

        $normalized = [];
        $errors = [];

        if ($requireAll) {
            foreach ($fields as $field) {
                if ($field->is_required && ! array_key_exists($field->key, $values)) {
                    $errors[$field->key] = 'فیلد '.$field->name.' الزامی است.';
                }
            }
        }

        foreach ($values as $key => $value) {
            $field = $fields->get($key);
            if (! $field) {
                $errors[$key] = 'فیلد سفارشی معتبر نیست.';

                continue;
            }

            [$normalizedValue, $error] = $this->normalizeValue($field, $value);

            if ($error) {
                $errors[$key] = $error;

                continue;
            }

            $normalized[$key] = $normalizedValue;
        }

        return [$normalized, $errors];
    }

    public function syncForWorkItem(WorkItem $workItem, array $values): void
    {
        if ($values === []) {
            return;
        }

        $fields = CustomField::query()
            ->where('tenant_id', $workItem->tenant_id)
            ->where('scope', 'work_item')
            ->whereIn('key', array_keys($values))
            ->where('is_active', true)
            ->get()
            ->keyBy('key');

        foreach ($values as $key => $value) {
            $field = $fields->get($key);
            if (! $field) {
                continue;
            }

            [$normalized, $error] = $this->normalizeValue($field, $value);
            if ($error) {
                continue;
            }

            if ($this->isEmptyValue($normalized)) {
                CustomFieldValue::query()
                    ->where('field_id', $field->getKey())
                    ->where('work_item_id', $workItem->getKey())
                    ->delete();

                continue;
            }

            CustomFieldValue::query()->updateOrCreate([
                'field_id' => $field->getKey(),
                'work_item_id' => $workItem->getKey(),
                'project_id' => $workItem->project_id,
            ], [
                'tenant_id' => $workItem->tenant_id,
                'value' => $normalized,
            ]);
        }
    }

    /**
     * @return array{0: mixed, 1: string|null}
     */
    protected function normalizeValue(CustomField $field, mixed $value): array
    {
        if ($value === null || $value === '') {
            return [null, null];
        }

        $type = $field->type;
        $settings = $field->settings ?? [];

        switch ($type) {
            case 'number':
                if (! is_numeric($value)) {
                    return [null, 'مقدار باید عددی باشد.'];
                }

                return [(float) $value, null];

            case 'boolean':
                $normalized = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($normalized === null) {
                    return [null, 'مقدار باید بولی باشد.'];
                }

                return [$normalized, null];

            case 'date':
                try {
                    return [Carbon::parse((string) $value)->toDateString(), null];
                } catch (\Throwable) {
                    return [null, 'تاریخ معتبر نیست.'];
                }

            case 'select':
                $options = (array) Arr::get($settings, 'options', []);
                $allowed = $options === [] ? [] : array_keys($options);
                if ($allowed !== [] && ! in_array($value, $allowed, true)) {
                    return [null, 'گزینه انتخابی معتبر نیست.'];
                }

                return [(string) $value, null];

            case 'multi_select':
                if (! is_array($value)) {
                    return [null, 'مقدار باید آرایه باشد.'];
                }

                $options = (array) Arr::get($settings, 'options', []);
                $allowed = $options === [] ? [] : array_keys($options);
                $filtered = $allowed === [] ? $value : array_values(array_intersect($value, $allowed));

                return [$filtered, null];

            case 'ai_field':
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return [$decoded, null];
                    }

                    return [$value, null];
                }

                return [$value, null];

            case 'textarea':
            case 'text':
            default:
                return [(string) $value, null];
        }
    }

    protected function isEmptyValue(mixed $value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_array($value)) {
            return $value === [];
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        return false;
    }
}
