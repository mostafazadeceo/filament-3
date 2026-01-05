<?php

namespace Haida\ThemeEngine;

class ThemeRegistry
{
    /** @var array<string, ThemeDefinition> */
    private array $themes = [];

    /**
     * @param  array<string, array<string, mixed>>  $definitions
     */
    public function __construct(array $definitions = [])
    {
        foreach ($definitions as $key => $definition) {
            $this->register(ThemeDefinition::fromArray($key, $definition));
        }
    }

    public function register(ThemeDefinition $theme): void
    {
        $this->themes[$theme->key] = $theme;
    }

    public function get(string $key): ?ThemeDefinition
    {
        return $this->themes[$key] ?? null;
    }

    /**
     * @return array<string, ThemeDefinition>
     */
    public function all(): array
    {
        return $this->themes;
    }
}
