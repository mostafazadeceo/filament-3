@php
    $settings = $settings ?? [];
    $promptEnabled = (bool) ($settings['prompt_enabled'] ?? true);
    $autoSubscribe = (bool) ($settings['auto_subscribe'] ?? true);
    $position = $settings['prompt_position'] ?? 'bottom-left';
    $repeatMinutes = (int) ($settings['prompt_repeat_minutes'] ?? 1440);
    $delaySeconds = (int) ($settings['prompt_delay_seconds'] ?? 2);
    $title = $settings['prompt_title'] ?? 'دریافت اعلان‌ها';
    $body = $settings['prompt_body'] ?? 'برای دریافت اطلاع‌رسانی‌های مهم، اجازه ارسال اعلان را فعال کنید.';
    $allowLabel = $settings['prompt_allow_label'] ?? 'فعال‌سازی';
    $dismissLabel = $settings['prompt_dismiss_label'] ?? 'بعداً';
    $linkLabel = $settings['prompt_link_label'] ?? null;
    $linkUrl = $settings['prompt_link_url'] ?? null;
    $imageUrl = $settings['prompt_image_url'] ?? null;
    $imageAlt = $settings['prompt_image_alt'] ?? null;
    $userId = $userId ?? null;
@endphp

<div
    id="fn-webpush-prompt"
    class="hidden fixed z-50 {{ $position === 'bottom-right' ? 'bottom-6 right-6' : '' }}{{ $position === 'bottom-left' ? 'bottom-6 left-6' : '' }}{{ $position === 'top-right' ? 'top-6 right-6' : '' }}{{ $position === 'top-left' ? 'top-6 left-6' : '' }}"
    style="max-width: 360px"
>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-lg">
        @if ($imageUrl)
            <div class="mb-3">
                <img src="{{ $imageUrl }}" alt="{{ $imageAlt ?? '' }}" class="w-full rounded-lg">
            </div>
        @endif
        <div class="text-sm font-semibold text-gray-900" id="fn-webpush-title">{{ $title }}</div>
        <div class="mt-1 text-sm text-gray-600" id="fn-webpush-body">{{ $body }}</div>

        @if ($linkLabel && $linkUrl)
            <div class="mt-2">
                <a href="{{ $linkUrl }}" target="_blank" class="text-sm text-primary-600 hover:underline">
                    {{ $linkLabel }}
                </a>
            </div>
        @endif

        <div id="fn-webpush-status" class="mt-2 text-xs text-gray-500"></div>

        <div class="mt-3 flex items-center gap-2">
            <button type="button" id="fn-webpush-allow" class="rounded-lg bg-primary-600 px-3 py-2 text-xs font-semibold text-white">
                {{ $allowLabel }}
            </button>
            <button type="button" id="fn-webpush-dismiss" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-700">
                {{ $dismissLabel }}
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const promptEl = document.getElementById('fn-webpush-prompt');
        const allowButton = document.getElementById('fn-webpush-allow');
        const dismissButton = document.getElementById('fn-webpush-dismiss');
        const statusEl = document.getElementById('fn-webpush-status');

        const config = {
            enabled: @json($promptEnabled),
            autoSubscribe: @json($autoSubscribe),
            repeatMinutes: @json($repeatMinutes),
            delaySeconds: @json($delaySeconds),
            vapidKey: @json($vapidPublicKey ?? null),
            subscribeEndpoint: @json($subscribeEndpoint ?? null),
            serviceWorkerPath: @json($serviceWorkerPath ?? null),
            userId: @json($userId),
        };

        const supportsWebPush = ('Notification' in window) && ('serviceWorker' in navigator) && ('PushManager' in window);
        const hasPrompt = Boolean(promptEl && allowButton && dismissButton);
        const storageKey = 'fn_webpush_prompt_last_dismissed_' + (config.userId ?? 'guest');

        const urlBase64ToUint8Array = (base64String) => {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            const outputArray = new Uint8Array(rawData.length);
            for (let i = 0; i < rawData.length; ++i) {
                outputArray[i] = rawData.charCodeAt(i);
            }
            return outputArray;
        };

        const setStatus = (target, text) => {
            if (target) {
                target.textContent = text || '';
            }
        };

        const hidePrompt = () => {
            if (promptEl) {
                promptEl.classList.add('hidden');
            }
        };

        const showPrompt = () => {
            if (promptEl) {
                promptEl.classList.remove('hidden');
            }
        };

        const shouldShowPrompt = () => {
            if (!config.enabled || !hasPrompt) {
                return false;
            }

            if (!config.vapidKey) {
                setStatus(statusEl, 'کلید VAPID تنظیم نشده است.');
                return false;
            }

            if (Notification.permission === 'granted') {
                return false;
            }

            if (Notification.permission === 'denied') {
                return false;
            }

            const lastDismissed = parseInt(localStorage.getItem(storageKey) || '0', 10);
            if (config.repeatMinutes > 0 && lastDismissed) {
                const diffMinutes = (Date.now() - lastDismissed) / 60000;
                if (diffMinutes < config.repeatMinutes) {
                    return false;
                }
            }

            return true;
        };

        const saveSubscription = async (subscription) => {
            if (!config.subscribeEndpoint) {
                throw new Error('مسیر ثبت وب‌پوش مشخص نیست.');
            }

            const response = await fetch(config.subscribeEndpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                },
                body: JSON.stringify({ subscription: subscription.toJSON() }),
            });

            if (!response.ok) {
                const payload = await response.json().catch(() => null);
                const message = payload?.message || payload?.error || response.statusText || 'ارسال اطلاعات وب‌پوش ناموفق بود.';
                throw new Error(message);
            }
        };

        const ensureSubscription = async (statusTarget) => {
            if (!config.vapidKey) {
                setStatus(statusTarget, 'کلید VAPID تنظیم نشده است.');
                return false;
            }

            if (!config.serviceWorkerPath) {
                setStatus(statusTarget, 'Service Worker مشخص نشده است.');
                return false;
            }

            const registration = await navigator.serviceWorker.register(config.serviceWorkerPath);
            let subscription = await registration.pushManager.getSubscription();
            if (!subscription) {
                subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: urlBase64ToUint8Array(config.vapidKey),
                });
            }

            await saveSubscription(subscription);
            return true;
        };

        const requestPermissionAndSubscribe = async (statusTarget) => {
            try {
                const permission = await Notification.requestPermission();
                if (permission !== 'granted') {
                    setStatus(statusTarget, 'دسترسی اعلان رد شد. برای فعال‌سازی از تنظیمات مرورگر اقدام کنید.');
                    return false;
                }

                const ok = await ensureSubscription(statusTarget);
                if (ok) {
                    setStatus(statusTarget, 'وب‌پوش فعال شد.');
                    if (statusTarget === statusEl) {
                        hidePrompt();
                    }
                }
                return ok;
            } catch (error) {
                const message = error?.message || 'خطا در فعال‌سازی وب‌پوش.';
                setStatus(statusTarget, message);
                return false;
            }
        };

        const initPrompt = async () => {
            if (!hasPrompt || !config.enabled) {
                return;
            }

            if (Notification.permission === 'denied') {
                setStatus(statusEl, 'دسترسی اعلان در مرورگر رد شده است.');
            }

            if (config.autoSubscribe && Notification.permission === 'granted') {
                await ensureSubscription(statusEl);
                return;
            }

            if (shouldShowPrompt()) {
                setTimeout(showPrompt, Math.max(0, config.delaySeconds) * 1000);
            }
        };

        const initPageButton = async () => {
            const pageButton = document.getElementById('fn-webpush-page-enable');
            const pageStatusEl = document.getElementById('fn-webpush-page-status');
            if (!pageButton) {
                return;
            }

            if (!supportsWebPush) {
                setStatus(pageStatusEl, 'مرورگر شما از وب‌پوش پشتیبانی نمی‌کند.');
                pageButton.disabled = true;
                return;
            }

            if (!pageButton.dataset.bound) {
                pageButton.dataset.bound = '1';
                pageButton.addEventListener('click', () => requestPermissionAndSubscribe(pageStatusEl));
            }

            if (!config.vapidKey) {
                setStatus(pageStatusEl, 'کلید VAPID تنظیم نشده است.');
                return;
            }

            if (Notification.permission === 'denied') {
                setStatus(pageStatusEl, 'دسترسی اعلان در مرورگر رد شده است.');
                return;
            }

            if (config.autoSubscribe && Notification.permission === 'granted') {
                await ensureSubscription(pageStatusEl);
                return;
            }

            setStatus(pageStatusEl, 'برای فعال‌سازی روی دکمه کلیک کنید.');
        };

        const initAll = async () => {
            if (supportsWebPush) {
                await initPrompt();
            } else if (hasPrompt) {
                setStatus(statusEl, 'مرورگر شما از وب‌پوش پشتیبانی نمی‌کند.');
            }

            await initPageButton();
        };

        initAll();
        document.addEventListener('livewire:navigated', initAll);
    })();
</script>
