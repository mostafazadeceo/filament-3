<x-filament::page>
    <form wire:submit.prevent="generateReport" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            بروزرسانی گزارش
        </x-filament::button>
    </form>

    <x-filament::section class="mt-6" heading="خلاصه دوره">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-sm text-gray-500">کارکرد (دقیقه)</div>
                <div class="text-lg font-semibold">{{ $summaryTotals['worked_minutes'] ?? 0 }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-sm text-gray-500">اضافه‌کار (دقیقه)</div>
                <div class="text-lg font-semibold">{{ $summaryTotals['overtime_minutes'] ?? 0 }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-sm text-gray-500">تأخیر (دقیقه)</div>
                <div class="text-lg font-semibold">{{ $summaryTotals['late_minutes'] ?? 0 }}</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="text-sm text-gray-500">غیبت (دقیقه)</div>
                <div class="text-lg font-semibold">{{ $summaryTotals['absence_minutes'] ?? 0 }}</div>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section class="mt-6" heading="شکاف پوشش برنامه">
        @if(empty($coverageGaps))
            <div class="text-sm text-gray-500">داده‌ای برای نمایش وجود ندارد.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-right text-gray-600">
                            <th class="py-2">تاریخ</th>
                            <th class="py-2">برنامه‌ریزی</th>
                            <th class="py-2">حضور ثبت‌شده</th>
                            <th class="py-2">شکاف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coverageGaps as $row)
                            <tr class="border-t">
                                <td class="py-2">{{ $row['work_date'] ?? '-' }}</td>
                                <td class="py-2">{{ $row['scheduled_count'] ?? 0 }}</td>
                                <td class="py-2">{{ $row['attended_count'] ?? 0 }}</td>
                                <td class="py-2">{{ $row['gap_count'] ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-filament::section>

    <x-filament::section class="mt-6" heading="گزارش مدیریتی هوش مصنوعی">
        @if(($aiReport['enabled'] ?? false) === true)
            <div class="space-y-3 text-sm">
                <div class="text-gray-700">{{ $aiReport['report'] ?? 'گزارش در دسترس نیست.' }}</div>
                @if(! empty($aiReport['highlights']))
                    <ul class="list-disc list-inside text-gray-600">
                        @foreach($aiReport['highlights'] as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @else
            <div class="text-sm text-gray-500">
                هوش مصنوعی غیرفعال است یا مجوز لازم برای استفاده وجود ندارد.
            </div>
        @endif
    </x-filament::section>
</x-filament::page>

