<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>سفارش رلوگرید {{ $order->trx }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; direction: rtl; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: right; }
        th { background: #f5f5f5; }
        .meta { margin-bottom: 12px; }
    </style>
</head>
<body>
    <div class="meta">
        <strong>شناسه تراکنش:</strong> {{ $order->trx }}<br>
        <strong>مرجع:</strong> {{ $order->reference }}<br>
        <strong>وضعیت:</strong> {{ \Haida\FilamentRelograde\Support\RelogradeLabels::orderStatus($order->order_status) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>محصول</th>
                <th>تعداد</th>
                <th>کد ووچر</th>
                <th>سریال ووچر</th>
                <th>نشانی ووچر</th>
                <th>تاریخ انقضا</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                @foreach ($item->lines as $line)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->amount }}</td>
                        <td>{{ $line->voucher_code }}</td>
                        <td>{{ $line->voucher_serial }}</td>
                        <td>{{ $line->voucher_url }}</td>
                        <td>{{ optional($line->voucher_date_expired)->toDateTimeString() }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
