<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

final class PermissionsPresenter
{
    /**
     * @param  array<int, string>  $keys
     * @return array<int, string>
     */
    public static function listWithLabels(array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $values[] = PermissionLabels::labelWithKey($key);
        }

        return $values;
    }

    /**
     * @param  array<int, string>  $values
     * @return array<int, string>
     */
    public static function normalizeList(array $values): array
    {
        $normalized = array_map([self::class, 'normalizeKey'], $values);

        return array_values(array_unique(array_filter($normalized)));
    }

    public static function normalizeKey(string $value): string
    {
        if (preg_match('/\\(([^)]+)\\)$/', $value, $matches)) {
            return $matches[1];
        }

        $labels = PermissionLabels::labels();
        $key = array_search($value, $labels, true);

        return $key !== false ? (string) $key : $value;
    }
}
