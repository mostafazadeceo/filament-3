<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Pages;

use Filamat\IamSuite\Filament\Concerns\AuthorizesIam;
use Filamat\IamSuite\Services\ModuleCatalog;
use Filamat\IamSuite\Services\OrganizationEntitlementService;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class TenantSettings extends Page
{
    use AuthorizesIam;
    use InteractsWithForms;

    protected static ?string $permission = 'iam.manage';

    protected static ?string $navigationLabel = 'تنظیمات';

    protected static ?string $title = 'تنظیمات فضای کاری';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    protected string $view = 'filamat-iam::pages.tenant-settings';

    public array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return ! TenantContext::shouldBypass();
    }

    public function mount(): void
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return;
        }

        $this->form->fill($tenant->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('عمومی')
                    ->schema([
                        TextInput::make('name')->label('نام')->required(),
                        TextInput::make('locale')->label('زبان')->nullable(),
                        TextInput::make('timezone')->label('منطقه زمانی')->nullable(),
                        TextInput::make('settings.brand_name')->label('نام برند')->nullable(),
                        TextInput::make('settings.logo_url')->label('آدرس لوگو')->nullable(),
                        TextInput::make('settings.primary_color')->label('رنگ اصلی')->nullable(),
                    ]),
                Section::make('امنیت')
                    ->schema([
                        TextInput::make('settings.security.ip_allowlist')
                            ->label('لیست آی‌پی مجاز (جداشده با ویرگول)')
                            ->nullable(),
                        TextInput::make('settings.security.password_policy')
                            ->label('سیاست رمز عبور')
                            ->nullable(),
                        TextInput::make('settings.otp.length')->label('طول رمز یکبارمصرف')->numeric()->nullable(),
                        TextInput::make('settings.otp.max_attempts')->label('حداکثر تلاش رمز یکبارمصرف')->numeric()->nullable(),
                    ]),
                Section::make('ویژگی‌ها')
                    ->schema([
                        Textarea::make('settings.allowed_features')->label('ویژگی‌های مجاز')->nullable(),
                    ]),
                Section::make('ماژول‌ها')
                    ->schema([
                        CheckboxList::make('settings.access.modules')
                            ->label('ماژول‌های فعال')
                            ->options(function () {
                                $tenant = TenantContext::getTenant();
                                if (! $tenant) {
                                    return [];
                                }

                                $options = app(ModuleCatalog::class)->moduleOptions();
                                $allowed = app(OrganizationEntitlementService::class)->allowedModulesForTenant($tenant);
                                $allowedMap = array_fill_keys($allowed, true);

                                return array_intersect_key($options, $allowedMap);
                            })
                            ->columns(2)
                            ->searchable()
                            ->helperText('فقط ماژول‌های مجاز توسط سازمان نمایش داده می‌شوند.'),
                    ]),
                Section::make('دسترسی‌ها')
                    ->schema([
                        Tabs::make('access_tabs')
                            ->schema([
                                Tab::make('شرکت')
                                    ->schema([
                                        Toggle::make('settings.access.company.enforce')
                                            ->label('اعمال محدودیت دسترسی برای شرکت')
                                            ->default(false),
                                        Select::make('settings.access.company.allowed_permissions')
                                            ->label('مجوزهای مجاز شرکت')
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => AccessSettings::permissionOptions(TenantContext::getTenant())),
                                        Select::make('settings.access.company.allowed_roles')
                                            ->label('نقش‌های مجاز شرکت')
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => AccessSettings::roleOptionMap(TenantContext::getTenant())),
                                    ]),
                                Tab::make('شخص')
                                    ->schema([
                                        Select::make('settings.access.person.default_permissions')
                                            ->label('مجوزهای پیش‌فرض کاربران')
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => AccessSettings::permissionOptions(TenantContext::getTenant())),
                                        Select::make('settings.access.person.default_roles')
                                            ->label('نقش‌های پیش‌فرض کاربران')
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => AccessSettings::roleOptionMap(TenantContext::getTenant())),
                                        Select::make('settings.access.person.allowed_permissions')
                                            ->label('مجوزهای قابل تخصیص به کاربران')
                                            ->multiple()
                                            ->searchable()
                                            ->options(fn () => AccessSettings::permissionOptions(TenantContext::getTenant())),
                                    ]),
                                Tab::make('پکیج‌ها')
                                    ->schema([
                                        Repeater::make('settings.access.packages')
                                            ->label('پکیج‌های دسترسی')
                                            ->itemLabel(fn (array $state) => $state['title'] ?? 'پکیج جدید')
                                            ->columns(2)
                                            ->schema([
                                                TextInput::make('key')->label('کلید پکیج')->required(),
                                                TextInput::make('title')->label('عنوان پکیج')->required(),
                                                Select::make('permissions')
                                                    ->label('مجوزها')
                                                    ->multiple()
                                                    ->searchable()
                                                    ->options(fn () => AccessSettings::permissionOptions(TenantContext::getTenant()))
                                                    ->columnSpanFull(),
                                                Select::make('roles')
                                                    ->label('نقش‌ها')
                                                    ->multiple()
                                                    ->searchable()
                                                    ->options(fn () => AccessSettings::roleOptionMap(TenantContext::getTenant()))
                                                    ->columnSpanFull(),
                                                KeyValue::make('features')->label('ویژگی‌ها')->nullable()->columnSpanFull(),
                                                KeyValue::make('quotas')->label('کوتاها')->nullable()->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return;
        }

        $tenant->update($this->form->getState());

        Notification::make()
            ->title('تنظیمات ذخیره شد')
            ->success()
            ->send();
    }
}
