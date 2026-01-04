<?php

namespace Haida\SiteBuilderCore\Enums;

enum SiteStatus: string
{
    case Draft = 'draft';
    case Preview = 'preview';
    case Published = 'published';
    case Disabled = 'disabled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'پیش نویس',
            self::Preview => 'پیش نمایش',
            self::Published => 'منتشر شده',
            self::Disabled => 'غیرفعال',
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
