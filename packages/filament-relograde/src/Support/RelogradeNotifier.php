<?php

namespace Haida\FilamentRelograde\Support;

use Filament\Notifications\Notification;
use Haida\FilamentRelograde\Exceptions\RelogradeApiException;
use Throwable;

class RelogradeNotifier
{
    public static function success(string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->title($title)
            ->success();

        if ($body) {
            $notification->body($body);
        }

        $notification->send();
    }

    public static function error(Throwable $exception, ?string $fallback = null): void
    {
        $title = $fallback ?: $exception->getMessage();

        if ($exception instanceof RelogradeApiException) {
            $title = $exception->getMessage();
        }

        Notification::make()
            ->title($title)
            ->danger()
            ->send();
    }
}
