@props([
    'label',
    'tooltip' => null,
])

@php
    use Filament\Support\Icons\Heroicon;

    $tooltipContent = filled($tooltip) ? $tooltip : null;
@endphp

<div class="fi-topbar-item">
    <button
        type="button"
        class="fi-topbar-item-btn"
        @if ($tooltipContent)
            x-tooltip="{ content: @js($tooltipContent), theme: $store.theme, allowHTML: true }"
        @endif
    >
        {{ \Filament\Support\generate_icon_html(
            Heroicon::OutlinedCalendarDays,
            attributes: (new \Illuminate\View\ComponentAttributeBag)->class(['fi-topbar-item-icon'])
        ) }}
        <span class="fi-topbar-item-label">
            {{ $label }}
        </span>
    </button>
</div>
