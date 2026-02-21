@php
    use Filament\Facades\Filament;

    $current = app()->getLocale();
    $redirect = url()->current();

    $panel = Filament::getCurrentPanel();
    $appsUrl = $panel
        ? $panel->route('app.switch', array_filter([
            'tenant' => $panel->hasTenancy() ? Filament::getTenant() : null,
        ], fn ($v) => $v !== null))
        : url('/');
@endphp

<div class="flex items-center gap-3">
    <a
        href="{{ $appsUrl }}"
        class="inline-flex items-center gap-2 rounded-full border border-sky-200/70 bg-white/70 px-3 py-2 text-[11px] font-semibold text-slate-700 shadow-sm hover:bg-white/90 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10"
        title="اپلیکیشن‌ها"
    >
        <x-filament::icon icon="heroicon-o-squares-2x2" class="h-4 w-4" />
        <span class="hidden sm:inline">اپلیکیشن‌ها</span>
    </a>

    <div class="inline-flex items-center gap-1 rounded-full border border-sky-200/70 bg-white/70 px-2 py-1 text-[11px] font-semibold text-slate-700 shadow-sm dark:border-white/10 dark:bg-white/5 dark:text-slate-200">
        <a
            href="{{ route('lang.switch', ['locale' => 'fa', 'redirect' => $redirect]) }}"
            class="rounded-full px-2 py-1 {{ $current === 'fa' ? 'bg-sky-100 text-sky-700 dark:bg-white/10 dark:text-sky-200' : 'hover:bg-sky-50 dark:hover:bg-white/5' }}"
        >فا</a>
        <a
            href="{{ route('lang.switch', ['locale' => 'ar', 'redirect' => $redirect]) }}"
            class="rounded-full px-2 py-1 {{ $current === 'ar' ? 'bg-sky-100 text-sky-700 dark:bg-white/10 dark:text-sky-200' : 'hover:bg-sky-50 dark:hover:bg-white/5' }}"
        >ع</a>
        <a
            href="{{ route('lang.switch', ['locale' => 'en', 'redirect' => $redirect]) }}"
            class="rounded-full px-2 py-1 {{ $current === 'en' ? 'bg-sky-100 text-sky-700 dark:bg-white/10 dark:text-sky-200' : 'hover:bg-sky-50 dark:hover:bg-white/5' }}"
        >EN</a>
    </div>

    <x-filament-panels::theme-switcher />
</div>
