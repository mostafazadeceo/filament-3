@php
    use Filament\Support\Icons\Heroicon;

    $user = filament()->auth()->user();
@endphp

@if ($user)
    <div class="fi-topbar-item">
        <div class="fi-topbar-item-btn" role="presentation">
            {{ \Filament\Support\generate_icon_html(
                Heroicon::OutlinedUserCircle,
                attributes: (new \Illuminate\View\ComponentAttributeBag)->class(['fi-topbar-item-icon'])
            ) }}
            <span class="fi-topbar-item-label">
                {{ __('filament-panels::widgets/account-widget.welcome', ['app' => config('app.name')]) }} —
                {{ filament()->getUserName($user) }}
            </span>
        </div>
    </div>

    <div class="fi-topbar-item">
        <form action="{{ filament()->getLogoutUrl() }}" method="post">
            @csrf
            <button type="submit" class="fi-topbar-item-btn">
                {{ \Filament\Support\generate_icon_html(
                    Heroicon::OutlinedArrowLeftEndOnRectangle,
                    alias: \Filament\View\PanelsIconAlias::WIDGETS_ACCOUNT_LOGOUT_BUTTON,
                    attributes: (new \Illuminate\View\ComponentAttributeBag)->class(['fi-topbar-item-icon'])
                ) }}
                <span class="fi-topbar-item-label">
                    {{ __('filament-panels::widgets/account-widget.actions.logout.label') }}
                </span>
            </button>
        </form>
    </div>
@endif
