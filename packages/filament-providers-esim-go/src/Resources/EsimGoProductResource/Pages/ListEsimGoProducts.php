<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources\EsimGoProductResource\Pages;

use Filamat\IamSuite\Filament\Resources\Pages\ListRecordsWithCreate;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoProductResource;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\Services\ProviderJobDispatcher;
use Haida\ProvidersCore\Support\ProviderAction;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;

class ListEsimGoProducts extends ListRecordsWithCreate
{
    protected static string $resource = EsimGoProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_catalogue')
                ->label('همگام‌سازی کاتالوگ')
                ->icon('heroicon-o-arrow-path')
                ->visible(fn () => IamAuthorization::allows('esim_go.catalogue.sync'))
                ->action(function (ProviderJobDispatcher $dispatcher) {
                    $connection = EsimGoConnection::query()->orderByDesc('id')->first();
                    if (! $connection) {
                        Notification::make()
                            ->title('اتصال eSIM Go یافت نشد')
                            ->body('ابتدا یک اتصال پیش‌فرض ایجاد کنید.')
                            ->warning()
                            ->send();

                        return;
                    }
                    if ($connection->status !== 'active') {
                        Notification::make()
                            ->title('اتصال فعال نیست')
                            ->body('اتصال انتخاب‌شده غیرفعال است. در صورت نیاز، ابتدا اتصال را تست کنید.')
                            ->warning()
                            ->send();
                    }

                    $context = new ProviderContext($connection->tenant_id, $connection->getKey(), (bool) ($connection->metadata['sandbox'] ?? false));
                    $dispatcher->dispatch(ProviderAction::SyncProducts, $context, 'esim-go', [
                        'force' => true,
                    ]);

                    Notification::make()
                        ->title('همگام‌سازی در صف قرار گرفت')
                        ->body('نتیجه پس از اتمام در لیست محصولات و اسنپ‌شات‌ها قابل مشاهده است.')
                        ->success()
                        ->send();
                }),
            Action::make('sync_catalogue_now')
                ->label('همگام‌سازی فوری')
                ->icon('heroicon-o-bolt')
                ->visible(fn () => IamAuthorization::allows('esim_go.catalogue.sync'))
                ->action(function (ProviderJobDispatcher $dispatcher) {
                    $connection = EsimGoConnection::query()->orderByDesc('id')->first();
                    if (! $connection) {
                        Notification::make()
                            ->title('اتصال eSIM Go یافت نشد')
                            ->body('ابتدا یک اتصال پیش‌فرض ایجاد کنید.')
                            ->warning()
                            ->send();

                        return;
                    }
                    if ($connection->status !== 'active') {
                        Notification::make()
                            ->title('اتصال فعال نیست')
                            ->body('اتصال انتخاب‌شده غیرفعال است. در صورت نیاز، ابتدا اتصال را تست کنید.')
                            ->warning()
                            ->send();
                    }

                    $context = new ProviderContext($connection->tenant_id, $connection->getKey(), (bool) ($connection->metadata['sandbox'] ?? false));

                    try {
                        $log = $dispatcher->dispatchSync(ProviderAction::SyncProducts, $context, 'esim-go', [
                            'force' => true,
                        ]);
                    } catch (\Throwable) {
                        Notification::make()
                            ->title('همگام‌سازی با خطا مواجه شد')
                            ->body('جزئیات خطا در لاگ‌های Provider قابل مشاهده است.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $log->refresh();
                    if ($log->status === 'failed') {
                        Notification::make()
                            ->title('همگام‌سازی ناموفق بود')
                            ->body($log->error_message ?: 'جزئیات خطا در لاگ‌های Provider قابل مشاهده است.')
                            ->danger()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('همگام‌سازی انجام شد')
                        ->body('محصولات و اسنپ‌شات‌ها به‌روز شدند.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
