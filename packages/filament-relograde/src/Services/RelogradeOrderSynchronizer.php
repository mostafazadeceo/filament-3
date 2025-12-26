<?php

namespace Haida\FilamentRelograde\Services;

use Carbon\Carbon;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeOrder;
use Haida\FilamentRelograde\Models\RelogradeOrderItem;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class RelogradeOrderSynchronizer
{
    public function sync(RelogradeConnection $connection, array $payload): RelogradeOrder
    {
        $orderData = $payload['data'] ?? $payload;
        $trx = data_get($orderData, 'trx');

        if (! $trx) {
            throw new RuntimeException('شناسه تراکنش در پاسخ رلوگرید موجود نیست.');
        }

        return DB::transaction(function () use ($connection, $orderData, $trx) {
            $existing = RelogradeOrder::query()->where('trx', $trx)->first();
            $downloaded = $existing?->downloaded ?? false;

            $order = RelogradeOrder::updateOrCreate([
                'trx' => $trx,
            ], [
                'connection_id' => $connection->getKey(),
                'reference' => data_get($orderData, 'reference'),
                'state' => data_get($orderData, 'state'),
                'type' => data_get($orderData, 'type', 'api'),
                'order_status' => data_get($orderData, 'orderStatus', data_get($orderData, 'status')),
                'payment_status' => data_get($orderData, 'paymentStatus'),
                'is_balance_payment' => (bool) data_get($orderData, 'isBalancePayment', true),
                'downloaded' => $downloaded,
                'payment_currency' => data_get($orderData, 'paymentCurrency'),
                'price_currency' => data_get($orderData, 'priceCurrency'),
                'price_amount' => data_get($orderData, 'priceAmount'),
                'price_vat' => data_get($orderData, 'priceVat'),
                'price_incl_vat' => data_get($orderData, 'priceInclVat'),
                'price_fx' => data_get($orderData, 'priceFx'),
                'date_created' => $this->parseDate(data_get($orderData, 'dateCreated')),
                'last_synced_at' => now(),
                'meta' => $orderData,
            ]);

            $order->items()->delete();

            $items = data_get($orderData, 'items', []);
            foreach ($items as $itemData) {
                $item = new RelogradeOrderItem([
                    'product_slug' => data_get($itemData, 'productSlug'),
                    'product_name' => data_get($itemData, 'productName'),
                    'brand' => data_get($itemData, 'brand'),
                    'product_type' => data_get($itemData, 'productType'),
                    'region' => data_get($itemData, 'region'),
                    'redeem_type' => data_get($itemData, 'redeemType'),
                    'main_category' => data_get($itemData, 'mainCategory'),
                    'amount' => (int) data_get($itemData, 'amount', 1),
                    'face_value_amount' => data_get($itemData, 'faceValueAmount'),
                    'face_value_currency' => data_get($itemData, 'faceValueCurrency'),
                    'face_value_fx' => data_get($itemData, 'faceValueFx'),
                    'single_price_amount' => data_get($itemData, 'singlePriceAmount'),
                    'total_price_amount' => data_get($itemData, 'totalPriceAmount'),
                    'total_price_vat' => data_get($itemData, 'totalPriceVat'),
                    'total_price_incl_vat' => data_get($itemData, 'totalPriceInclVat'),
                    'price_fx' => data_get($itemData, 'priceFx'),
                    'payment_currency' => data_get($itemData, 'paymentCurrency'),
                    'single_price_amount_in_payment_currency' => data_get($itemData, 'singlePriceAmountInPaymentCurrency'),
                    'total_price_amount_in_payment_currency' => data_get($itemData, 'totalPriceAmountInPaymentCurrency'),
                    'total_price_vat_in_payment_currency' => data_get($itemData, 'totalPriceVatInPaymentCurrency'),
                    'total_price_incl_vat_in_payment_currency' => data_get($itemData, 'totalPriceInclVatInPaymentCurrency'),
                    'lines_completed' => (int) data_get($itemData, 'linesCompleted', 0),
                    'raw_json' => $itemData,
                ]);

                $order->items()->save($item);

                $lines = data_get($itemData, 'orderLines', data_get($itemData, 'lines', []));
                if (! is_array($lines)) {
                    $lines = [];
                }

                foreach ($lines as $lineData) {
                    $item->lines()->create([
                        'tag' => data_get($lineData, 'tag'),
                        'status' => data_get($lineData, 'status'),
                        'voucher_code' => data_get($lineData, 'voucherCode'),
                        'voucher_serial' => data_get($lineData, 'voucherSerial'),
                        'voucher_date_expired' => $this->parseDate(data_get($lineData, 'voucherDateExpired')),
                        'token' => data_get($lineData, 'token'),
                        'voucher_url' => data_get($lineData, 'voucherUrl'),
                        'raw_json' => $lineData,
                    ]);
                }
            }

            return $order->fresh(['items.lines']);
        });
    }

    protected function parseDate($value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
