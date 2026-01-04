<x-filament::page>
    <div class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-600">تنخواه</label>
            <select wire:model="fundId" class="rounded-xl border-gray-300 text-sm">
                <option value="">همه تنخواه‌ها</option>
                @foreach ($this->getFundOptions() as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>

            <x-filament::button wire:click="runAudit">
                اجرای تحلیل هوشمند
            </x-filament::button>
        </div>

        <x-filament::section heading="نمای کلی">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">استثناهای باز</div>
                    <div class="text-lg font-semibold">{{ $summary['exceptions_open'] ?? 0 }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">ناهنجاری‌های هوشمند</div>
                    <div class="text-lg font-semibold">{{ $summary['ai_anomalies'] ?? 0 }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">تنخواه انتخابی</div>
                    <div class="text-lg font-semibold">
                        {{ $fundId ? ($this->getFundOptions()[$fundId] ?? 'نامشخص') : 'همه' }}
                    </div>
                </div>
            </div>
        </x-filament::section>

        <div class="grid gap-6 lg:grid-cols-2">
            <x-filament::section heading="استثناهای کنترل">
                @if (empty($exceptions))
                    <div class="text-sm text-gray-500">داده‌ای برای نمایش وجود ندارد.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-right text-gray-600">
                                    <th class="py-2">عنوان</th>
                                    <th class="py-2">شدت</th>
                                    <th class="py-2">وضعیت</th>
                                    <th class="py-2">تنخواه</th>
                                    <th class="py-2">تاریخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($exceptions as $row)
                                    <tr class="border-t">
                                        <td class="py-2">{{ $row['title'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['severity'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['status'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['fund'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['detected_at'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>

            <x-filament::section heading="ناهنجاری‌های هوشمند">
                @if (empty($anomalies))
                    <div class="text-sm text-gray-500">ناهنجاری فعالی ثبت نشده است.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-right text-gray-600">
                                    <th class="py-2">ریسک</th>
                                    <th class="py-2">وضعیت</th>
                                    <th class="py-2">مرجع</th>
                                    <th class="py-2">مبلغ</th>
                                    <th class="py-2">تاریخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($anomalies as $row)
                                    <tr class="border-t">
                                        <td class="py-2">{{ $row['score'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['status'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['expense_reference'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['amount'] ?? '-' }}</td>
                                        <td class="py-2">{{ $row['date'] ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-filament::section>
        </div>
    </div>
</x-filament::page>
