<?php

namespace Haida\ContentCms\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\ContentCms\Policies\CmsPagePolicy;

final class ContentCmsCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'content-cms',
            self::permissions(),
            [
                'cms' => true,
            ],
            [],
            [
                CmsPagePolicy::class,
            ],
            [
                'cms' => 'مدیریت محتوا',
                'cms_pages' => 'صفحات',
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
            'cms.page.view',
            'cms.page.manage',
        ];
    }
}
