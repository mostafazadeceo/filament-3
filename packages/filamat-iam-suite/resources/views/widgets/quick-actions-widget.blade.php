<x-filament-widgets::widget class="fi-wi-quick-actions w-full" style="grid-column: 1 / -1;">
    <x-filament::section>
        <x-slot name="heading">
            اقدامات سریع
        </x-slot>
        <x-slot name="afterHeader">
            @if($canManage)
                <x-filament::link href="{{ $manageUrl }}" color="gray">
                    مدیریت
                </x-filament::link>
            @endif
        </x-slot>

        @if(count($actions))
            <div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach($actions as $action)
                    <a
                        href="{{ $action['url'] }}"
                        class="group block w-full rounded-xl border border-gray-200/70 bg-white p-4 shadow-sm transition hover:border-primary-200 hover:shadow-md dark:border-gray-800 dark:bg-gray-900"
                    >
                        <div class="flex items-start gap-3">
                            <div class="rounded-lg bg-primary-50 p-2 text-primary-600 transition group-hover:bg-primary-100 dark:bg-primary-500/10 dark:text-primary-300">
                                <x-filament::icon icon="{{ $action['icon'] }}" class="h-5 w-5" />
                            </div>
                            <div>
                                <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
                                    رتبه {{ $action['rank'] }}
                                </div>
                                <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $action['label'] }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $action['description'] }}
                                </div>
                                @if(! empty($action['editUrl']))
                                    <div class="mt-2">
                                        <x-filament::link href="{{ $action['editUrl'] }}" color="gray">
                                            ویرایش
                                        </x-filament::link>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            @if($canCreate)
                <a
                    href="{{ $addUrl }}"
                    class="group flex w-full items-center justify-center rounded-xl border border-dashed border-gray-300/70 bg-white p-8 text-sm font-semibold text-gray-600 transition hover:border-primary-200 hover:text-primary-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:text-primary-300"
                >
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-plus" class="h-6 w-6" />
                        <span>افزودن دسترسی سریع</span>
                    </div>
                </a>
            @else
                <div class="rounded-xl border border-dashed border-gray-300/70 bg-white p-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                    دسترسی لازم برای افزودن اقدام سریع وجود ندارد.
                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
