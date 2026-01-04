<?php

namespace Haida\FilamentThreeCx\Support;

use Filament\Notifications\Notification;
use Haida\FilamentThreeCx\Exceptions\ThreeCxApiException;
use Throwable;

class ThreeCxNotifier
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

        if ($exception instanceof ThreeCxApiException) {
            $title = $exception->getMessage();
        }

        Notification::make()
            ->title($title)
            ->danger()
            ->send();
    }
}
