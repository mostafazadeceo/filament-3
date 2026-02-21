<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Http\Controllers\Api\V1;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\PermissionLabels;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FeatureGates\Services\FeatureGateService;
use Illuminate\Http\Request;

class CapabilityController
{
    public function index(Request $request)
    {
        if (! interface_exists(CapabilityRegistryInterface::class)) {
            return response()->json(['permissions' => [], 'navigation' => [], 'feature_flags' => []]);
        }

        $registry = app(CapabilityRegistryInterface::class);
        $tenant = TenantContext::getTenant();
        $user = $request->user();
        $featureGate = class_exists(FeatureGateService::class) ? app(FeatureGateService::class) : null;

        $permissions = [];
        $navigation = [];
        $featureFlags = [];

        foreach ($registry->all() as $capability) {
            foreach ($capability->permissions as $permission) {
                if ($user && IamAuthorization::allows($permission, $tenant, $user)) {
                    $permissions[$permission] = PermissionLabels::label($permission);
                }
            }

            foreach ($capability->navigation as $key => $label) {
                $navigation[$key] = $label;
            }

            foreach ($capability->featureFlags as $key => $flag) {
                if ($featureGate && $tenant) {
                    $decision = $featureGate->evaluate($tenant, $key, user: $user);
                    $featureFlags[$key] = $decision->allowed;
                } else {
                    $featureFlags[$key] = (bool) $flag;
                }
            }
        }

        return response()->json([
            'permissions' => collect($permissions)->map(fn ($label, $key) => [
                'key' => $key,
                'label' => $label,
            ])->values(),
            'navigation' => $navigation,
            'feature_flags' => $featureFlags,
        ]);
    }
}
