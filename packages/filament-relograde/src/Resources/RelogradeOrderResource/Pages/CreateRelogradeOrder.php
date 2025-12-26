<?php

namespace Haida\FilamentRelograde\Resources\RelogradeOrderResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeProduct;
use Haida\FilamentRelograde\Resources\RelogradeOrderResource;
use Haida\FilamentRelograde\Services\RelogradeOrderService;
use Haida\FilamentRelograde\Support\RelogradeNotifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateRelogradeOrder extends CreateRecord
{
    protected static string $resource = RelogradeOrderResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $connection = $this->resolveConnection($data['connection_id'] ?? null);
        if (! $connection) {
            throw ValidationException::withMessages([
                'connection_id' => 'اتصال معتبری پیدا نشد.',
            ]);
        }

        $itemsPayload = $this->buildItemsPayload($data['items'] ?? [], $connection);

        $payload = [
            'reference' => $data['reference'] ?? null,
            'paymentCurrency' => $data['payment_currency'] ?? null,
            'items' => $itemsPayload,
        ];

        $fulfillment = $data['fulfillment_policy'] ?? null;

        try {
            /** @var RelogradeOrderService $service */
            $service = app(RelogradeOrderService::class);

            return $service->createOrder($connection, $payload, $fulfillment);
        } catch (\Throwable $exception) {
            RelogradeNotifier::error($exception, 'ایجاد سفارش ناموفق بود.');
            throw ValidationException::withMessages([
                'items' => $exception->getMessage(),
            ]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('view', ['record' => $this->record]);
    }

    protected function resolveConnection(?int $connectionId): ?RelogradeConnection
    {
        if ($connectionId) {
            return RelogradeConnection::find($connectionId);
        }

        return RelogradeConnection::query()->default()->first();
    }

    protected function buildItemsPayload(array $items, RelogradeConnection $connection): array
    {
        if (count($items) === 0) {
            throw ValidationException::withMessages([
                'items' => 'حداقل یک قلم الزامی است.',
            ]);
        }

        $payload = [];
        foreach ($items as $index => $item) {
            $slug = $item['product_slug'] ?? null;
            $amount = (int) ($item['amount'] ?? 0);
            $faceValue = $item['face_value'] ?? null;

            if (! $slug) {
                throw ValidationException::withMessages([
                    "items.{$index}.product_slug" => 'محصول الزامی است.',
                ]);
            }

            $product = RelogradeProduct::query()
                ->where('connection_id', $connection->getKey())
                ->where('slug', $slug)
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    "items.{$index}.product_slug" => 'محصول پیدا نشد.',
                ]);
            }

            if ($amount < 1) {
                throw ValidationException::withMessages([
                    "items.{$index}.amount" => 'تعداد باید حداقل ۱ باشد.',
                ]);
            }

            if ($amount > 1000) {
                throw ValidationException::withMessages([
                    "items.{$index}.amount" => 'تعداد نباید بیشتر از ۱۰۰۰ باشد.',
                ]);
            }

            $payloadItem = [
                'productSlug' => $slug,
                'amount' => $amount,
            ];

            if ($product->is_variable_product) {
                if ($faceValue === null || $faceValue === '') {
                    throw ValidationException::withMessages([
                        "items.{$index}.face_value" => 'برای محصولات متغیر، ارزش اسمی الزامی است.',
                    ]);
                }

                $value = (float) $faceValue;
                if ($product->face_value_min !== null && $value < (float) $product->face_value_min) {
                    throw ValidationException::withMessages([
                        "items.{$index}.face_value" => 'ارزش اسمی کمتر از حداقل است.',
                    ]);
                }

                if ($product->face_value_max !== null && $value > (float) $product->face_value_max) {
                    throw ValidationException::withMessages([
                        "items.{$index}.face_value" => 'ارزش اسمی بیشتر از حداکثر است.',
                    ]);
                }

                $payloadItem['faceValue'] = $value;
            }

            $payload[] = $payloadItem;
        }

        return $payload;
    }
}
