<?php

namespace Haida\PageBuilder\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\PageBuilder\Policies\PageTemplatePolicy;

final class PageBuilderCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'page-builder',
            self::permissions(),
            [
                'page_builder' => true,
            ],
            [],
            [
                PageTemplatePolicy::class,
            ],
            [
                'page_builder' => 'صفحه ساز',
                'page_builder_templates' => 'قالب ها',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'page_builder.template.view',
            'page_builder.template.manage',
        ];
    }
}
