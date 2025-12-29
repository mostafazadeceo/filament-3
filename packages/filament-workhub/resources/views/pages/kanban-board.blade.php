<x-filament-panels::page>
    <div x-data="{ draggingId: null }" class="space-y-6">
        <div class="flex flex-wrap items-center gap-3">
            <label class="text-sm font-medium text-gray-600">پروژه</label>
            <select wire:model="projectId" class="rounded-xl border-gray-300 text-sm">
                <option value="">انتخاب پروژه</option>
                @foreach ($this->getProjectOptions() as $option)
                    <option value="{{ $option['id'] }}">{{ $option['label'] }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            @forelse ($columns as $column)
                <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full" style="background-color: {{ $column['status']['color'] ?? '#94a3b8' }}"></span>
                            <span class="text-sm font-semibold text-gray-700">{{ $column['status']['name'] }}</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ count($column['items']) }}</span>
                    </div>

                    <div
                        class="flex flex-col gap-3"
                        x-on:dragover.prevent
                        x-on:drop.prevent="if (draggingId) { $wire.moveWorkItem(draggingId, {{ $column['status']['id'] }}); draggingId = null; }"
                    >
                        @forelse ($column['items'] as $item)
                            <div
                                class="rounded-xl border border-gray-200 bg-gray-50 p-3 shadow-sm cursor-move"
                                draggable="true"
                                x-on:dragstart="draggingId = {{ $item['id'] }}"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-semibold text-gray-500">{{ $item['key'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $item['priority'] }}</span>
                                </div>
                                <div class="mt-2 text-sm font-medium text-gray-800">{{ $item['title'] }}</div>
                                @if (! empty($item['assignee']))
                                    <div class="mt-2 text-xs text-gray-500">مسئول: {{ $item['assignee'] }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-gray-200 p-3 text-center text-xs text-gray-400">
                                آیتمی وجود ندارد
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                    ابتدا پروژه‌ای انتخاب کنید.
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
