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

            <label class="text-sm font-medium text-gray-600">بازه (روز)</label>
            <input type="number" min="7" max="365" wire:model="rangeDays" class="w-24 rounded-xl border-gray-300 text-sm" />

            <div class="text-sm text-gray-500">
                {{ $report['period']['from'] ?? '' }} تا {{ $report['period']['to'] ?? '' }}
            </div>
        </div>

        <x-filament::section heading="خلاصه مدیریتی">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">تعداد هزینه‌ها</div>
                    <div class="text-lg font-semibold">{{ $report['totals']['count'] ?? 0 }}</div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">جمع مبلغ</div>
                    <div class="text-lg font-semibold">
                        {{ number_format($report['totals']['amount'] ?? 0, 0) }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">تغییر مبلغ</div>
                    <div class="text-lg font-semibold">
                        {{ number_format($report['totals']['amount_delta'] ?? 0, 0) }}
                        @if(isset($report['totals']['amount_delta_percent']))
                            <span class="text-xs text-gray-500">
                                ({{ number_format($report['totals']['amount_delta_percent'], 1) }}%)
                            </span>
                        @endif
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">مبلغ در معرض ریسک</div>
                    <div class="text-lg font-semibold">
                        {{ number_format($report['controls']['amount_at_risk'] ?? 0, 0) }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">میانگین زمان بستن (روز)</div>
                    <div class="text-lg font-semibold">
                        {{ $report['controls']['avg_close_days'] ?? '-' }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">استثناهای باز</div>
                    <div class="text-lg font-semibold">
                        {{ $report['controls']['exceptions_open'] ?? 0 }}
                    </div>
                </div>
                <div class="rounded-lg border border-gray-200 p-4">
                    <div class="text-sm text-gray-500">ناهنجاری‌های هوشمند</div>
                    <div class="text-lg font-semibold">
                        {{ $report['controls']['ai_anomalies'] ?? 0 }}
                    </div>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament::page>
