<x-filament::section>
    <x-slot name="heading">
        اقدامات سریع
    </x-slot>

    @if(count($actions))
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($actions as $action)
                <a
                    href="{{ $action['url'] }}"
                    class="group rounded-xl border border-gray-200/70 bg-white p-4 shadow-sm transition hover:border-primary-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
                >
                    <div class="flex items-start gap-3">
                        <div class="rounded-lg bg-primary-50 p-2 text-primary-600 transition group-hover:bg-primary-100 dark:bg-primary-500/10 dark:text-primary-300">
                            <x-filament::icon icon="{{ $action['icon'] }}" class="h-5 w-5" />
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $action['label'] }}
                            </div>
                            <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ $action['description'] }}
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <x-filament::empty-state
            heading="هیچ اقدامی در دسترس نیست"
            description="مجوزهای فعلی شما اجازه اقدام سریع را نمی‌دهد."
            icon="heroicon-o-lock-closed"
        />
    @endif
</x-filament::section>
