<?php

namespace Haida\FilamentNotify\Core\Support\Conditions;

class ConditionEvaluator
{
    public function passes(?array $conditions, array $context): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $match = $conditions['match'] ?? 'all';
        $rules = $conditions['rules'] ?? $conditions;

        if (! is_array($rules)) {
            return true;
        }

        $results = [];

        foreach ($rules as $rule) {
            if (! is_array($rule)) {
                continue;
            }

            $field = $rule['field'] ?? null;
            $operator = $rule['operator'] ?? 'equals';
            $value = $rule['value'] ?? null;

            if (! $field) {
                continue;
            }

            $actual = data_get($context, $field);
            $results[] = $this->compare($actual, $operator, $value);
        }

        if (empty($results)) {
            return true;
        }

        if ($match === 'any') {
            return in_array(true, $results, true);
        }

        return ! in_array(false, $results, true);
    }

    protected function compare(mixed $actual, string $operator, mixed $value): bool
    {
        return match ($operator) {
            'equals' => $actual == $value,
            'not_equals' => $actual != $value,
            'contains' => is_string($actual) && is_string($value) && str_contains($actual, $value),
            'in' => is_array($value) && in_array($actual, $value, true),
            'not_in' => is_array($value) && ! in_array($actual, $value, true),
            'gt' => is_numeric($actual) && is_numeric($value) && $actual > $value,
            'gte' => is_numeric($actual) && is_numeric($value) && $actual >= $value,
            'lt' => is_numeric($actual) && is_numeric($value) && $actual < $value,
            'lte' => is_numeric($actual) && is_numeric($value) && $actual <= $value,
            'exists' => ! is_null($actual),
            'empty' => empty($actual),
            default => $actual == $value,
        };
    }
}
