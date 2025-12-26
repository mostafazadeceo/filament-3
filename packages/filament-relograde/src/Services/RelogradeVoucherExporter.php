<?php

namespace Haida\FilamentRelograde\Services;

use Haida\FilamentRelograde\Models\RelogradeOrder;
use RuntimeException;

class RelogradeVoucherExporter
{
    public function exportCsv(RelogradeOrder $order)
    {
        $order->loadMissing('items.lines');

        $headers = [
            'شناسه تراکنش',
            'محصول',
            'تعداد',
            'کد ووچر',
            'سریال ووچر',
            'نشانی ووچر',
            'تاریخ انقضا',
        ];

        $callback = function () use ($order, $headers) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $headers);

            foreach ($order->items as $item) {
                foreach ($item->lines as $line) {
                    fputcsv($handle, [
                        $order->trx,
                        $item->product_name,
                        $item->amount,
                        $line->voucher_code,
                        $line->voucher_serial,
                        $line->voucher_url,
                        optional($line->voucher_date_expired)->toDateTimeString(),
                    ]);
                }
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, 'سفارش-رلوگرید-'.$order->trx.'.csv');
    }

    public function exportPdf(RelogradeOrder $order)
    {
        if (! class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            throw new RuntimeException('برای خروجی پی‌دی‌اف باید بسته barryvdh/laravel-dompdf نصب شود.');
        }

        $order->loadMissing('items.lines');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('filament-relograde::vouchers.pdf', [
            'order' => $order,
        ]);

        return $pdf->download('سفارش-رلوگرید-'.$order->trx.'.pdf');
    }
}
