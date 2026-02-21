<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Services\ModuleCatalog;
use Filamat\IamSuite\Services\OrganizationProvisioningService;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Filamat\IamSuite\Support\PermissionLabels;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class MegaAdminOnboarding extends Page
{
    use AuthorizesIam;
    use InteractsWithForms;

    protected static ?string $permission = 'iam.manage';

    protected static ?string $navigationLabel = 'ایجاد سازمان (ویزارد)';

    protected static ?string $title = 'ایجاد سازمان';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'مگا سوپر ادمین';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filamat-iam::pages.mega-admin-onboarding';

    public array $data = [];

    public static function canAccess(): bool
    {
        return MegaSuperAdmin::check(auth()->user()) && parent::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return MegaSuperAdmin::check(auth()->user());
    }

    public function mount(): void
    {
        $this->form->fill([
            'shared_data_mode' => 'isolated',
            'plan_status' => 'active',
            'tenant_status' => 'active',
            'organization_owner_id' => null,
            'tenant_owner_id' => null,
            'modules_config' => $this->defaultModulesConfig(),
            'tenant_owner_same_as_org' => true,
            'plan_template_id' => null,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $userModel = config('auth.providers.users.model');
        $userTable = (new $userModel)->getTable();
        $userCreateForm = [
            TextInput::make('name')
                ->label('نام')
                ->maxLength(255)
                ->required(),
            TextInput::make('email')
                ->label('ایمیل')
                ->email()
                ->maxLength(255)
                ->rules(['unique:'.$userTable.',email'])
                ->required(),
            TextInput::make('password')
                ->label('رمز عبور')
                ->password()
                ->revealable()
                ->minLength(8)
                ->maxLength(255)
                ->required(),
        ];

        $moduleSections = $this->buildModuleSections();

        return $schema
            ->schema([
                Wizard::make([
                    Step::make('سازمان')
                        ->schema([
                            TextInput::make('organization_name')->label('نام سازمان')->required(),
                            Select::make('organization_owner_id')
                                ->label('سوپرادمین سازمان')
                                ->options(fn () => $userModel::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                ->searchable()
                                ->required()
                                ->createOptionForm($userCreateForm)
                                ->createOptionAction(fn (Action $action) => $action->label('ایجاد سوپرادمین جدید'))
                                ->createOptionUsing(function (array $data) use ($userModel) {
                                    $user = $userModel::query()->create([
                                        'name' => $data['name'],
                                        'email' => $data['email'],
                                        'password' => $data['password'],
                                    ]);

                                    return $user->getKey();
                                })
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                    if ($get('tenant_owner_same_as_org')) {
                                        $set('tenant_owner_id', $state);
                                    }
                                }),
                            Select::make('shared_data_mode')
                                ->label('حالت داده مشترک')
                                ->options([
                                    'isolated' => 'ایزوله',
                                    'shared_by_organization' => 'اشتراکی در سازمان',
                                ])
                                ->required(),
                        ]),
                    Step::make('ماژول‌ها')
                        ->schema(array_merge([
                            Select::make('plan_template_id')
                                ->label('پلن آماده (اختیاری)')
                                ->options(fn () => $this->planTemplateOptions())
                                ->searchable()
                                ->live()
                                ->helperText('انتخاب پلن آماده، ماژول‌ها و سهمیه‌ها را پر می‌کند و قابل ویرایش باقی می‌ماند.')
                                ->afterStateUpdated(fn ($state, Set $set) => $this->applyPlanTemplate($state, $set)),
                        ], $moduleSections)),
                    Step::make('اشتراک')
                        ->schema([
                            TextInput::make('plan_name')->label('عنوان پلن')->nullable(),
                            Select::make('plan_status')
                                ->label('وضعیت پلن')
                                ->options([
                                    'active' => 'فعال',
                                    'trial' => 'آزمایشی',
                                    'inactive' => 'غیرفعال',
                                ])
                                ->required(),
                            DateTimePicker::make('plan_starts_at')->label('شروع پلن')->nullable(),
                            DateTimePicker::make('plan_ends_at')->label('پایان پلن')->nullable(),
                            DateTimePicker::make('trial_ends_at')->label('پایان دوره آزمایشی')->nullable(),
                            TextInput::make('max_tenants')->label('حداکثر فضای کاری')->numeric()->nullable(),
                            TextInput::make('max_users')->label('حداکثر کاربران')->numeric()->nullable(),
                            Textarea::make('plan_notes')->label('یادداشت')->nullable(),
                        ]),
                    Step::make('فضای کاری اولیه')
                        ->schema([
                            TextInput::make('tenant_name')->label('نام فضای کاری')->required(),
                            TextInput::make('tenant_slug')->label('شناسه فضای کاری')->required(),
                            Toggle::make('tenant_owner_same_as_org')
                                ->label('مالک فضای کاری همان سوپرادمین سازمان است')
                                ->default(true)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get): void {
                                    if ($state) {
                                        $set('tenant_owner_id', $get('organization_owner_id'));
                                    }
                                }),
                            Select::make('tenant_owner_id')
                                ->label('مالک فضای کاری')
                                ->options(fn () => $userModel::query()->orderBy('name')->pluck('name', 'id')->toArray())
                                ->searchable()
                                ->required(fn (Get $get): bool => ! $get('tenant_owner_same_as_org'))
                                ->visible(fn (Get $get): bool => ! $get('tenant_owner_same_as_org'))
                                ->createOptionForm($userCreateForm)
                                ->createOptionAction(fn (Action $action) => $action->label('ایجاد مالک جدید'))
                                ->createOptionUsing(function (array $data) use ($userModel) {
                                    $user = $userModel::query()->create([
                                        'name' => $data['name'],
                                        'email' => $data['email'],
                                        'password' => $data['password'],
                                    ]);

                                    return $user->getKey();
                                }),
                            Select::make('tenant_status')
                                ->label('وضعیت')
                                ->options([
                                    'active' => 'فعال',
                                    'inactive' => 'غیرفعال',
                                ])
                                ->required(),
                            TextInput::make('tenant_locale')->label('زبان')->nullable(),
                            TextInput::make('tenant_timezone')->label('منطقه زمانی')->nullable(),
                        ]),
                ])->statePath('data'),
            ]);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $actor = auth()->user();
        if (! $actor) {
            return;
        }

        $tenantOwnerId = ! empty($data['tenant_owner_same_as_org'])
            ? ($data['organization_owner_id'] ?? null)
            : ($data['tenant_owner_id'] ?? null);

        $moduleEntitlements = $this->resolveModuleEntitlements((array) ($data['modules_config'] ?? []));

        $payload = [
            'organization_name' => $data['organization_name'] ?? null,
            'organization_owner_id' => $data['organization_owner_id'] ?? null,
            'shared_data_mode' => $data['shared_data_mode'] ?? 'isolated',
            'modules' => $moduleEntitlements['modules'],
            'entitlements' => [
                'plan' => $data['plan_name'] ?? null,
                'status' => $data['plan_status'] ?? 'active',
                'starts_at' => $data['plan_starts_at'] ?? null,
                'ends_at' => $data['plan_ends_at'] ?? null,
                'trial_ends_at' => $data['trial_ends_at'] ?? null,
                'max_tenants' => $data['max_tenants'] ?? null,
                'max_users' => $data['max_users'] ?? null,
                'modules' => $moduleEntitlements['modules'],
                'modules_explicit' => true,
                'permissions' => $moduleEntitlements['permissions'],
                'feature_flags' => $moduleEntitlements['feature_flags'],
                'quotas' => $moduleEntitlements['quotas'],
                'notes' => $data['plan_notes'] ?? null,
            ],
            'tenant_owner_id' => $tenantOwnerId,
            'tenant' => [
                'name' => $data['tenant_name'] ?? null,
                'slug' => $data['tenant_slug'] ?? null,
                'status' => $data['tenant_status'] ?? 'active',
                'locale' => $data['tenant_locale'] ?? null,
                'timezone' => $data['tenant_timezone'] ?? null,
                'settings' => [
                    'access' => [
                        'modules' => $moduleEntitlements['modules'],
                    ],
                ],
            ],
        ];

        app(OrganizationProvisioningService::class)->createOrganizationWithTenant($payload, $actor);

        Notification::make()
            ->title('سازمان و فضای کاری ایجاد شد')
            ->success()
            ->send();

        $this->form->fill([
            'shared_data_mode' => 'isolated',
            'plan_status' => 'active',
            'tenant_status' => 'active',
            'organization_owner_id' => null,
            'tenant_owner_id' => null,
            'modules_config' => $this->defaultModulesConfig(),
            'tenant_owner_same_as_org' => true,
            'plan_template_id' => null,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultModulesConfig(): array
    {
        $modules = app(ModuleCatalog::class)->modules();
        $defaults = [];

        foreach ($modules as $moduleKey => $module) {
            $quotaDefaults = $this->quotaDefaultsFromDefinition((array) ($module['quotas'] ?? []));
            $hasQuotaDefaults = ($quotaDefaults['plan'] !== [] || $quotaDefaults['trial'] !== []);
            $flags = [];
            foreach ((array) ($module['feature_flags'] ?? []) as $flagKey => $flagValue) {
                if (is_int($flagKey)) {
                    $flags[(string) $flagValue] = true;
                } else {
                    $flags[(string) $flagKey] = (bool) $flagValue;
                }
            }

            $defaults[$moduleKey] = [
                'enabled' => true,
                'access_mode' => 'full',
                'permissions' => [],
                'feature_flags' => $flags,
                'quotas_enabled' => $hasQuotaDefaults,
                'quotas' => $quotaDefaults,
            ];
        }

        return $defaults;
    }

    /**
     * @return array<int|string, string>
     */
    private function planTemplateOptions(): array
    {
        return SubscriptionPlan::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->mapWithKeys(fn (SubscriptionPlan $plan): array => [
                $plan->getKey() => trim($plan->name.' ('.$plan->code.')'),
            ])
            ->all();
    }

    private function applyPlanTemplate(mixed $planId, Set $set): void
    {
        if (! is_numeric($planId)) {
            return;
        }

        $plan = SubscriptionPlan::query()->find((int) $planId);
        if (! $plan) {
            return;
        }

        $set('modules_config', $this->resolveModulesConfigFromPlan($plan));
        $set('plan_name', $plan->name);
        $set('plan_status', (int) ($plan->trial_days ?? 0) > 0 ? 'trial' : 'active');

        $startsAt = Carbon::now();
        $set('plan_starts_at', $startsAt->toDateTimeString());
        $set('plan_ends_at', (int) ($plan->period_days ?? 0) > 0
            ? $startsAt->copy()->addDays((int) $plan->period_days)->toDateTimeString()
            : null);
        $set('trial_ends_at', (int) ($plan->trial_days ?? 0) > 0
            ? $startsAt->copy()->addDays((int) $plan->trial_days)->toDateTimeString()
            : null);

        $chatPlanUsers = data_get($plan->features, 'quotas.chat.plan.max_users');
        $set('max_users', $plan->seat_limit ?? (is_numeric($chatPlanUsers) ? (int) $chatPlanUsers : null));
        $set('max_tenants', $plan->module_limit);
        $set('plan_notes', 'برگرفته از پلن آماده: '.$plan->name.' ('.$plan->code.')');
    }

    /**
     * @return array<string, mixed>
     */
    private function resolveModulesConfigFromPlan(SubscriptionPlan $plan): array
    {
        $defaults = $this->defaultModulesConfig();
        $catalog = app(ModuleCatalog::class)->modules();
        $features = (array) ($plan->features ?? []);

        $modules = array_values(array_filter(array_map('strval', Arr::wrap($features['modules'] ?? []))));
        $permissions = array_values(array_filter(array_map('strval', Arr::wrap($features['permissions'] ?? []))));
        $moduleFlags = (array) ($features['flags'] ?? []);
        $moduleQuotas = (array) ($features['quotas'] ?? []);

        $legacyChat = $this->normalizeQuotaValues((array) ($features['chat'] ?? []));
        if ($legacyChat !== [] && ! isset($moduleQuotas['chat'])) {
            $moduleQuotas['chat'] = [
                'plan' => $legacyChat,
                'trial' => [],
            ];
        }

        $permissionsByModule = [];
        $moduleCatalog = app(ModuleCatalog::class);
        foreach ($permissions as $permission) {
            $moduleKey = $moduleCatalog->moduleForPermission($permission);
            if (! $moduleKey) {
                continue;
            }

            $permissionsByModule[$moduleKey] ??= [];
            $permissionsByModule[$moduleKey][] = $permission;
            $modules[] = $moduleKey;
        }

        $modules = array_values(array_unique($modules));

        foreach ($defaults as $moduleKey => &$moduleConfig) {
            $enabled = in_array($moduleKey, $modules, true)
                || isset($permissionsByModule[$moduleKey])
                || isset($moduleFlags[$moduleKey])
                || isset($moduleQuotas[$moduleKey]);

            $moduleConfig['enabled'] = $enabled;
            if (! $enabled) {
                $moduleConfig['access_mode'] = 'full';
                $moduleConfig['permissions'] = [];
                $moduleConfig['quotas_enabled'] = false;
                $moduleConfig['quotas']['plan'] = [];
                $moduleConfig['quotas']['trial'] = [];
                continue;
            }

            $allPermissions = Arr::wrap($catalog[$moduleKey]['permissions'] ?? []);
            $selectedPermissions = array_values(array_unique(array_filter(Arr::wrap($permissionsByModule[$moduleKey] ?? []))));
            sort($allPermissions);
            sort($selectedPermissions);

            if ($selectedPermissions !== [] && $selectedPermissions !== $allPermissions) {
                $moduleConfig['access_mode'] = 'custom';
                $moduleConfig['permissions'] = $selectedPermissions;
            } else {
                $moduleConfig['access_mode'] = 'full';
                $moduleConfig['permissions'] = [];
            }

            if (array_key_exists($moduleKey, $moduleFlags)) {
                $moduleConfig['feature_flags'] = $this->normalizeModuleFlagsFromPlan(
                    $moduleFlags[$moduleKey],
                    (array) ($moduleConfig['feature_flags'] ?? [])
                );
            }

            $planQuotas = $this->normalizeQuotaValues((array) data_get($moduleQuotas, $moduleKey.'.plan', []));
            $trialQuotas = $this->normalizeQuotaValues((array) data_get($moduleQuotas, $moduleKey.'.trial', []));

            $moduleConfig['quotas']['plan'] = $planQuotas;
            $moduleConfig['quotas']['trial'] = $trialQuotas;
            $moduleConfig['quotas_enabled'] = ($planQuotas !== [] || $trialQuotas !== []);
        }
        unset($moduleConfig);

        return $defaults;
    }

    /**
     * @param  array<string, bool>  $defaults
     * @return array<string, bool>
     */
    private function normalizeModuleFlagsFromPlan(mixed $value, array $defaults): array
    {
        $normalized = $defaults;

        if (! is_array($value)) {
            return $normalized;
        }

        if (Arr::isAssoc($value)) {
            foreach ($value as $flag => $enabled) {
                $normalized[(string) $flag] = (bool) $enabled;
            }

            return $normalized;
        }

        $enabledFlags = array_values(array_filter(array_map('strval', $value)));
        foreach ($normalized as $flag => $enabled) {
            $normalized[$flag] = in_array($flag, $enabledFlags, true);
        }

        foreach ($enabledFlags as $flag) {
            $normalized[$flag] = true;
        }

        return $normalized;
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Section>
     */
    private function buildModuleSections(): array
    {
        $modules = app(ModuleCatalog::class)->modules();
        $sections = [];

        foreach ($modules as $moduleKey => $module) {
            $label = (string) ($module['label'] ?? $moduleKey);
            $permissions = Arr::wrap($module['permissions'] ?? []);
            $permissionOptions = $this->permissionOptions($permissions);
            $featureFlags = (array) ($module['feature_flags'] ?? []);

            $hasPermissions = $permissions !== [];
            $accessOptions = $hasPermissions
                ? [
                    'full' => 'دسترسی کامل',
                    'custom' => 'انتخاب امکانات',
                ]
                : [
                    'full' => 'دسترسی کامل',
                ];

            $sectionSchema = [
                Toggle::make("modules_config.{$moduleKey}.enabled")
                    ->label('فعال')
                    ->default(true)
                    ->live(),
                ToggleButtons::make("modules_config.{$moduleKey}.access_mode")
                    ->label('سطح دسترسی')
                    ->options($accessOptions)
                    ->default('full')
                    ->inline()
                    ->live()
                    ->visible(fn (Get $get): bool => (bool) $get("modules_config.{$moduleKey}.enabled"))
                    ->disabled(! $hasPermissions)
                    ->helperText($hasPermissions ? null : 'این ماژول مجوز قابل تفکیک ندارد.'),
            ];

            if ($permissions !== []) {
                $sectionSchema[] = CheckboxList::make("modules_config.{$moduleKey}.permissions")
                    ->label('امکانات مجاز')
                    ->options($permissionOptions)
                    ->columns(2)
                    ->searchable()
                    ->columnSpanFull()
                    ->required(fn (Get $get): bool => (bool) $get("modules_config.{$moduleKey}.enabled")
                        && $get("modules_config.{$moduleKey}.access_mode") === 'custom')
                    ->visible(fn (Get $get): bool => (bool) $get("modules_config.{$moduleKey}.enabled")
                        && $get("modules_config.{$moduleKey}.access_mode") === 'custom');
            }

            if ($featureFlags !== []) {
                foreach ($featureFlags as $flagKey => $flagValue) {
                    $flagName = is_int($flagKey) ? (string) $flagValue : (string) $flagKey;
                    $sectionSchema[] = Toggle::make("modules_config.{$moduleKey}.feature_flags.{$flagName}")
                        ->label(Str::headline($flagName))
                        ->default(is_int($flagKey) ? true : (bool) $flagValue)
                        ->visible(fn (Get $get): bool => (bool) $get("modules_config.{$moduleKey}.enabled"));
                }
            }

            $sectionSchema[] = Toggle::make("modules_config.{$moduleKey}.quotas_enabled")
                ->label('تنظیم ظرفیت ماژول')
                ->default(false)
                ->live()
                ->visible(fn (Get $get): bool => (bool) $get("modules_config.{$moduleKey}.enabled"));

            $sectionSchema[] = KeyValue::make("modules_config.{$moduleKey}.quotas.plan")
                ->label('ظرفیت‌های پلن')
                ->keyLabel('کلید ظرفیت')
                ->valueLabel('سقف')
                ->helperText($moduleKey === 'chat' ? 'کلیدهای پیشنهادی چت: max_users، max_channels، max_private_rooms' : null)
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => (bool) $get("modules_config.{$moduleKey}.enabled")
                    && (bool) $get("modules_config.{$moduleKey}.quotas_enabled"));

            $sectionSchema[] = KeyValue::make("modules_config.{$moduleKey}.quotas.trial")
                ->label('ظرفیت‌های دوره آزمایشی')
                ->keyLabel('کلید ظرفیت')
                ->valueLabel('سقف')
                ->helperText($moduleKey === 'chat' ? 'کلیدهای پیشنهادی چت: max_users، max_channels، max_private_rooms' : null)
                ->columnSpanFull()
                ->visible(fn (Get $get): bool => (bool) $get("modules_config.{$moduleKey}.enabled")
                    && (bool) $get("modules_config.{$moduleKey}.quotas_enabled"));

            $sections[] = Section::make($label)
                ->description($moduleKey)
                ->schema($sectionSchema)
                ->columns(2)
                ->collapsible()
                ->collapsed();
        }

        return $sections;
    }

    /**
     * @param  array<int, string>  $permissions
     * @return array<string, string>
     */
    private function permissionOptions(array $permissions): array
    {
        $labels = PermissionLabels::labels();
        $options = [];

        foreach ($permissions as $permission) {
            $options[$permission] = $labels[$permission]
                ?? Str::headline(str_replace(['.', '_'], ' ', $permission));
        }

        return $options;
    }

    /**
     * @param  array<string, mixed>  $modulesConfig
     * @return array{modules: array<int, string>, permissions: array<int, string>, feature_flags: array<string, array<int, string>>, quotas: array<string, array<string, array<string, int>>>}
     */
    private function resolveModuleEntitlements(array $modulesConfig): array
    {
        $catalog = app(ModuleCatalog::class)->modules();
        $modules = [];
        $permissions = [];
        $featureFlags = [];
        $quotas = [];

        foreach ($catalog as $moduleKey => $module) {
            $config = (array) ($modulesConfig[$moduleKey] ?? []);
            $enabled = (bool) ($config['enabled'] ?? false);
            if (! $enabled) {
                continue;
            }

            $modules[] = $moduleKey;

            $mode = (string) ($config['access_mode'] ?? 'full');
            $modulePermissions = Arr::wrap($module['permissions'] ?? []);
            if ($mode === 'custom') {
                $modulePermissions = array_values(array_filter(Arr::wrap($config['permissions'] ?? [])));
            }

            $permissions = array_merge($permissions, $modulePermissions);

            $flags = (array) ($config['feature_flags'] ?? []);
            if ($flags !== []) {
                $featureFlags[$moduleKey] = array_keys(array_filter(
                    $flags,
                    static fn ($value): bool => (bool) $value
                ));
            }

            $quotaConfig = (array) ($config['quotas'] ?? []);
            $planQuotas = $this->normalizeQuotaValues((array) ($quotaConfig['plan'] ?? []));
            $trialQuotas = $this->normalizeQuotaValues((array) ($quotaConfig['trial'] ?? []));

            if ($planQuotas !== [] || $trialQuotas !== []) {
                $quotas[$moduleKey] = [
                    'plan' => $planQuotas,
                    'trial' => $trialQuotas,
                ];
            }
        }

        $modules = array_values(array_unique($modules));
        sort($modules);

        $permissions = array_values(array_unique(array_filter($permissions)));
        sort($permissions);

        return [
            'modules' => $modules,
            'permissions' => $permissions,
            'feature_flags' => $featureFlags,
            'quotas' => $quotas,
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, int>
     */
    private function normalizeQuotaValues(array $values): array
    {
        $normalized = [];

        foreach ($values as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (! is_numeric($value)) {
                continue;
            }

            $normalized[(string) $key] = (int) $value;
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $definition
     * @return array{plan: array<string, string|int|float>, trial: array<string, string|int|float>}
     */
    private function quotaDefaultsFromDefinition(array $definition): array
    {
        $plan = [];
        $trial = [];

        if (array_key_exists('plan', $definition) || array_key_exists('trial', $definition)) {
            $plan = $this->formatQuotaDefaults((array) ($definition['plan'] ?? []));
            $trial = $this->formatQuotaDefaults((array) ($definition['trial'] ?? []));
        } else {
            $plan = $this->formatQuotaDefaults($definition);
        }

        return [
            'plan' => $plan,
            'trial' => $trial,
        ];
    }

    /**
     * @param  array<int|string, mixed>  $values
     * @return array<string, string|int|float>
     */
    private function formatQuotaDefaults(array $values): array
    {
        $formatted = [];

        foreach ($values as $key => $value) {
            $quotaKey = is_int($key) ? (string) $value : (string) $key;
            if ($quotaKey === '') {
                continue;
            }

            if (is_numeric($value)) {
                $formatted[$quotaKey] = $value + 0;
            } else {
                $formatted[$quotaKey] = '';
            }
        }

        return $formatted;
    }
}
