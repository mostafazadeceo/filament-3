<?php

namespace Haida\FilamentRelograde\Resources\RelogradeOrderResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource;
use Haida\FilamentRelograde\Services\RelogradeOrderService;
use Haida\FilamentRelograde\Services\RelogradeVoucherExporter;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeNotifier;

class ViewRelogradeOrder extends ViewRecord
{
    protected static string $resource = RelogradeOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm')
                ->label('تایید')
                ->icon('heroicon-o-check')
                ->visible(fn (RelogradeOrder $record) => $record->order_status === 'created' && RelogradeAuthorization::can('orders_fulfill'))
                ->requiresConfirmation()
                ->modalHeading('تایید سفارش')
                ->modalSubmitActionLabel('تایید')
                ->modalCancelActionLabel('انصراف')
                ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                    $service->confirmOrder($record);
                    RelogradeNotifier::success('سفارش تایید شد.');
                }),
            Action::make('resolve')
                ->label('نهایی‌سازی')
                ->icon('heroicon-o-sparkles')
                ->visible(fn (RelogradeOrder $record) => $record->order_status === 'created' && $record->items()->count() === 1 && RelogradeAuthorization::can('orders_fulfill'))
                ->requiresConfirmation()
                ->modalHeading('نهایی‌سازی سفارش')
                ->modalSubmitActionLabel('نهایی‌سازی')
                ->modalCancelActionLabel('انصراف')
                ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                    $service->resolveOrder($record);
                    RelogradeNotifier::success('سفارش نهایی شد.');
                }),
            Action::make('cancel')
                ->label('لغو')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (RelogradeOrder $record) => in_array($record->order_status, ['created', 'pending'], true))
                ->requiresConfirmation()
                ->modalHeading('لغو سفارش')
                ->modalSubmitActionLabel('لغو')
                ->modalCancelActionLabel('انصراف')
                ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                    $service->cancelOrder($record);
                    RelogradeNotifier::success('سفارش لغو شد.');
                }),
            Action::make('refresh')
                ->label('به‌روزرسانی')
                ->icon('heroicon-o-arrow-path')
                ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                    $service->refreshOrder($record);
                    RelogradeNotifier::success('سفارش به‌روزرسانی شد.');
                }),
            Action::make('poll')
                ->label('بررسی')
                ->icon('heroicon-o-clock')
                ->visible(fn (RelogradeOrder $record) => $record->order_status === 'pending')
                ->action(function (RelogradeOrder $record, RelogradeOrderService $service) {
                    $service->refreshOrder($record);
                    RelogradeNotifier::success('سفارش بررسی شد.');
                }),
            Action::make('mark_downloaded')
                ->label('علامت‌گذاری به‌عنوان دانلودشده')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn (RelogradeOrder $record) => ! $record->downloaded)
                ->action(function (RelogradeOrder $record) {
                    $record->downloaded = true;
                    $record->save();
                    RelogradeNotifier::success('سفارش به‌عنوان دانلودشده علامت‌گذاری شد.');
                }),
            Action::make('export_csv')
                ->label('خروجی سی‌اس‌وی')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn () => RelogradeAuthorization::can('vouchers_reveal'))
                ->action(function (RelogradeOrder $record, RelogradeVoucherExporter $exporter) {
                    return $exporter->exportCsv($record);
                }),
            Action::make('export_pdf')
                ->label('خروجی پی‌دی‌اف')
                ->icon('heroicon-o-document')
                ->visible(fn () => RelogradeAuthorization::can('vouchers_reveal'))
                ->action(function (RelogradeOrder $record, RelogradeVoucherExporter $exporter) {
                    return $exporter->exportPdf($record);
                }),
        ];
    }

    protected static function maskVoucher(?string $value): string
    {
        if (! $value) {
            return '';
        }

        $length = strlen($value);
        if ($length <= 4) {
            return str_repeat('*', $length);
        }

        return str_repeat('*', $length - 4).substr($value, -4);
    }
}
