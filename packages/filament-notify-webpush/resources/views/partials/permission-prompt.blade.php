@php
    $settings = $settings ?? [];
    $promptEnabled = (bool) ($settings['prompt_enabled'] ?? true);
    $autoSubscribe = (bool) ($settings['auto_subscribe'] ?? true);
    $position = $settings['prompt_position'] ?? 'bottom-left';
    $repeatMinutes = (int) ($settings['prompt_repeat_minutes'] ?? 1440);
    $delaySeconds = (int) ($settings['prompt_delay_seconds'] ?? 2);
    // Auto hide the prompt after a few seconds (0 disables).
    $autoDismissSeconds = (int) ($settings['prompt_auto_dismiss_seconds'] ?? 15);
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
    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4 shadow-lg" style="background:#f0f9ff;border-color:#bae6fd;">
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

        <div id="fn-webpush-timer" class="mt-2 hidden">
            <div class="flex items-center justify-between text-[11px] text-sky-700">
                <span>این پیام تا <span id="fn-webpush-timer-seconds"></span> ثانیه دیگر بسته می‌شود</span>
            </div>
            <div class="mt-1 h-1 w-full overflow-hidden rounded-full bg-sky-100" style="background:#e0f2fe;">
                <div id="fn-webpush-timer-bar" class="h-1 w-full rounded-full bg-sky-400" style="width: 100%;background:#38bdf8;"></div>
            </div>
        </div>

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
        const timerEl = document.getElementById('fn-webpush-timer');
        const timerSecondsEl = document.getElementById('fn-webpush-timer-seconds');
        const timerBarEl = document.getElementById('fn-webpush-timer-bar');

        const config = {
            enabled: @json($promptEnabled),
            autoSubscribe: @json($autoSubscribe),
            repeatMinutes: @json($repeatMinutes),
            delaySeconds: @json($delaySeconds),
            autoDismissSeconds: @json(max(0, $autoDismissSeconds)),
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
            stopAutoDismiss();
            if (promptEl) {
                promptEl.classList.add('hidden');
            }
        };

        const showPrompt = () => {
            if (promptEl) {
                promptEl.classList.remove('hidden');
                startAutoDismiss();
            }
        };

        let autoDismissIntervalId = null;
        let autoDismissEndAt = null;
        let autoDismissTotalMs = null;

        const stopAutoDismiss = () => {
            if (autoDismissIntervalId) {
                clearInterval(autoDismissIntervalId);
            }
            autoDismissIntervalId = null;
            autoDismissEndAt = null;
            autoDismissTotalMs = null;

            if (timerEl) {
                timerEl.classList.add('hidden');
            }
        };

        const dismissPrompt = () => {
            try {
                localStorage.setItem(storageKey, Date.now().toString());
            } catch (error) {}
            hidePrompt();
        };

        const startAutoDismiss = () => {
            stopAutoDismiss();

            if (!config.autoDismissSeconds || config.autoDismissSeconds <= 0) {
                return;
            }

            if (!timerEl || !timerSecondsEl || !timerBarEl) {
                return;
            }

            timerEl.classList.remove('hidden');
            autoDismissTotalMs = config.autoDismissSeconds * 1000;
            autoDismissEndAt = Date.now() + autoDismissTotalMs;

            const tick = () => {
                const remainingMs = Math.max(0, autoDismissEndAt - Date.now());
                const remainingSeconds = Math.ceil(remainingMs / 1000);
                timerSecondsEl.textContent = String(remainingSeconds);

                const pct = autoDismissTotalMs > 0 ? (remainingMs / autoDismissTotalMs) : 0;
                timerBarEl.style.width = (Math.max(0, Math.min(1, pct)) * 100).toFixed(1) + '%';

                if (remainingMs <= 0) {
                    dismissPrompt();
                }
            };

            tick();
            autoDismissIntervalId = setInterval(tick, 250);
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

        const bindPromptButtons = () => {
            if (!hasPrompt) {
                return;
            }

            if (!supportsWebPush) {
                setStatus(statusEl, 'مرورگر شما از وب‌پوش پشتیبانی نمی‌کند.');
                allowButton.disabled = true;
            }

            if (!allowButton.dataset.bound) {
                allowButton.dataset.bound = '1';
                allowButton.addEventListener('click', async () => {
                    stopAutoDismiss();
                    allowButton.disabled = true;
                    dismissButton.disabled = true;
                    try {
                        await requestPermissionAndSubscribe(statusEl);
                    } finally {
                        allowButton.disabled = false;
                        dismissButton.disabled = false;
                    }
                });
            }

            if (!dismissButton.dataset.bound) {
                dismissButton.dataset.bound = '1';
                dismissButton.addEventListener('click', () => {
                    dismissPrompt();
                });
            }
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

            bindPromptButtons();

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
            bindPromptButtons();

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
