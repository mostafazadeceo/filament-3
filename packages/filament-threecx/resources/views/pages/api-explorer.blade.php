<x-filament::page>
    <form wire:submit.prevent="sendRequest" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            ارسال درخواست
        </x-filament::button>
    </form>

    @if($response !== null || $error)
        <x-filament::section class="mt-6" heading="نتیجه">
            <div class="space-y-2 text-sm">
                <div class="text-gray-600">
                    وضعیت: {{ $statusCode ?? '-' }}
                    <span class="mx-2">|</span>
                    زمان: {{ $durationMs !== null ? $durationMs . 'ms' : '-' }}
                </div>

                @if($error)
                    <div class="text-danger-600">
                        {{ $error }}
                    </div>
                @endif

                <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 overflow-auto text-xs">{{ json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </x-filament::section>
    @endif
</x-filament::page>
