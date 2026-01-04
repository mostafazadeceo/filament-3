<div class="filamat-iam-impersonation-banner" style="background:#fef3c7;color:#92400e;padding:8px 16px;text-align:center;font-weight:600;">
    <span>در حال ورود به حساب کاربر {{ $session?->impersonated?->name ?? '...' }} هستید</span>
    <a href="{{ url('filamat-iam/impersonation/stop') }}" style="margin-right:12px;color:#b45309;text-decoration:underline;">خروج از امپرسونیشن</a>
</div>
