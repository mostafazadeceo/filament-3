<x-filament-panels::page>
    <div class="space-y-3">
        @forelse ($this->items as $item)
            <div class="rounded-lg border p-3 text-sm">{{ json_encode($item, JSON_UNESCAPED_UNICODE) }}</div>
        @empty
            <div class="rounded-lg border p-3 text-sm text-gray-500">No records</div>
        @endforelse
    </div>
</x-filament-panels::page>
