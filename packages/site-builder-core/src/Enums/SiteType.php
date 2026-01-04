<?php

namespace Haida\SiteBuilderCore\Enums;

enum SiteType: string
{
    case Website = 'website';
    case Blog = 'blog';
    case Store = 'store';
    case Mixed = 'mixed';

    public function label(): string
    {
        return match ($this) {
            self::Website => 'وب سایت',
            self::Blog => 'وبلاگ',
            self::Store => 'فروشگاه',
            self::Mixed => 'ترکیبی',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
