@php
    $user = auth()->user();
    $roleLabel = null;

    if ($user) {
        if (\Filamat\IamSuite\Support\MegaSuperAdmin::check($user)) {
            $roleLabel = 'مگا سوپرادمین';
        } else {
            $tenant = \Filamat\IamSuite\Support\TenantContext::getTenant();
            if ($tenant) {
                if (\Filamat\IamSuite\Support\OrganizationAccess::isCurrentOrganizationOwner($user)) {
                    $roleLabel = 'سوپرادمین سازمان';
                } else {
                    $pivot = method_exists($user, 'tenants')
                        ? $user->tenants()->where('tenants.id', $tenant->getKey())->first()
                        : null;
                    $pivotRole = $pivot?->pivot?->role;

                    $roleLabel = match ($pivotRole) {
                        'owner' => 'مالک فضای کاری',
                        'admin' => 'مدیر فضای کاری',
                        'member' => 'عضو',
                        default => 'کاربر',
                    };
                }
            }
        }
    }
@endphp

@if ($roleLabel)
    <div class="px-4 pt-2">
        <div class="rounded-lg bg-gray-50/70 px-3 py-2 text-xs font-medium text-gray-600 dark:bg-gray-900/40 dark:text-gray-300">
            نقش: {{ $roleLabel }}
        </div>
    </div>
@endif
