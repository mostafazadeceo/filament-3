<?php

namespace Haida\Blog\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\Blog\Policies\BlogCategoryPolicy;
use Haida\Blog\Policies\BlogPostPolicy;
use Haida\Blog\Policies\BlogTagPolicy;

final class BlogCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'blog',
            self::permissions(),
            [
                'blog' => true,
            ],
            [],
            [
                BlogPostPolicy::class,
                BlogCategoryPolicy::class,
                BlogTagPolicy::class,
            ],
            [
                'blog' => 'وبلاگ',
                'blog_posts' => 'نوشته ها',
                'blog_categories' => 'دسته بندی ها',
                'blog_tags' => 'برچسب ها',
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
            'blog.post.view',
            'blog.post.manage',
            'blog.category.manage',
            'blog.tag.manage',
        ];
    }
}
