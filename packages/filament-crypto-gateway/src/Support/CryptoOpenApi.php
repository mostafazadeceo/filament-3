<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Support;

class CryptoOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Crypto Gateway API',
                'version' => '0.1.0',
            ],
            'paths' => [
                '/api/v1/crypto/invoices' => [
                    'post' => ['summary' => 'ایجاد فاکتور رمزارز'],
                ],
                '/api/v1/crypto/invoices/{invoice}' => [
                    'get' => ['summary' => 'مشاهده فاکتور'],
                ],
                '/api/v1/crypto/invoices/{invoice}/status' => [
                    'get' => ['summary' => 'وضعیت فاکتور'],
                ],
                '/api/v1/crypto/invoices/{invoice}/refresh' => [
                    'post' => ['summary' => 'به‌روزرسانی فاکتور از درگاه'],
                ],
                '/api/v1/crypto/payouts' => [
                    'post' => ['summary' => 'ایجاد برداشت'],
                ],
                '/api/v1/crypto/payouts/{payout}' => [
                    'get' => ['summary' => 'نمایش برداشت'],
                ],
                '/api/v1/crypto/payouts/{payout}/approve' => [
                    'post' => ['summary' => 'تایید برداشت رمزارز'],
                ],
                '/api/v1/crypto/payouts/{payout}/reject' => [
                    'post' => ['summary' => 'رد برداشت رمزارز'],
                ],
                '/api/v1/crypto/payout-destinations' => [
                    'get' => ['summary' => 'لیست مقاصد برداشت'],
                    'post' => ['summary' => 'ایجاد مقصد برداشت'],
                ],
                '/api/v1/crypto/payout-destinations/{destination}' => [
                    'get' => ['summary' => 'نمایش مقصد برداشت'],
                    'put' => ['summary' => 'ویرایش مقصد برداشت'],
                    'delete' => ['summary' => 'حذف مقصد برداشت'],
                ],
                '/api/v1/crypto/webhooks/{provider}' => [
                    'post' => ['summary' => 'وبهوک درگاه رمزارز'],
                ],
                '/api/v1/crypto/rates' => [
                    'get' => ['summary' => 'نرخ تبدیل رمزارز'],
                ],
                '/api/v1/crypto/policy' => [
                    'get' => ['summary' => 'مشاهده پلن و کارمزد'],
                ],
                '/api/v1/crypto/health/providers' => [
                    'get' => ['summary' => 'سلامت درگاه‌ها'],
                ],
                '/api/v1/crypto/health/nodes' => [
                    'get' => ['summary' => 'سلامت نودها'],
                ],
                '/api/v1/crypto/reconcile/run' => [
                    'post' => ['summary' => 'اجرای آشتی‌سازی'],
                ],
                '/api/v1/crypto/openapi' => [
                    'get' => ['summary' => 'مشاهده OpenAPI'],
                ],
            ],
        ];
    }
}
