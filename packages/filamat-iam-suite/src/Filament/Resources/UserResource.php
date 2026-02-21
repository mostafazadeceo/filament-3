<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\UserResource\Pages\CreateUser;
use Filamat\IamSuite\Filament\Resources\UserResource\Pages\EditUser;
use Filamat\IamSuite\Filament\Resources\UserResource\Pages\ListUsers;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserApiKeysRelationManager;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserGroupsRelationManager;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserMfaMethodsRelationManager;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserPermissionOverridesRelationManager;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserSessionsRelationManager;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserTenantsRelationManager;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserTokensRelationManager;
use Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers\UserWalletsRelationManager;
use Filamat\IamSuite\Models\OtpCode;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\ImpersonationService;
use Filamat\IamSuite\Services\ProtectedActionService;
use Filamat\IamSuite\Services\SecurityEventService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends IamResource
{
    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = null;

    protected static ?string $tenantOwnershipRelationshipName = 'tenants';

    protected static ?string $navigationLabel = 'کاربران';

    protected static ?string $pluralModelLabel = 'کاربران';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (TenantContext::shouldBypass()) {
            return $query;
        }

        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            return $query;
        }

        return $query->whereHas('tenants', function (Builder $builder) use ($tenantId) {
            $builder->where('tenants.id', $tenantId);
        });
    }

    public static function getModel(): string
    {
        return config('auth.providers.users.model');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('نام')->required(),
                TextInput::make('email')->label('ایمیل')->email()->required(),
                Toggle::make('is_super_admin')->label('سوپرادمین')->visible(fn () => TenantContext::shouldBypass()),
                TextInput::make('password')
                    ->label('رمز عبور')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('email')->label('ایمیل')->searchable(),
                TextColumn::make('last_login_at')->label('آخرین ورود'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                Action::make('resetOtp')
                    ->label('ریست OTP')
                    ->requiresConfirmation()
                    ->visible(fn () => IamAuthorization::allowsAny(['iam.manage', 'user.reset_otp']))
                    ->action(function ($record) {
                        $tenantId = TenantContext::getTenantId();
                        OtpCode::query()
                            ->where('user_id', $record->getKey())
                            ->when($tenantId, fn ($query) => $query->where('tenant_id', $tenantId))
                            ->delete();

                        if (method_exists($record, 'forceFill')) {
                            $record->forceFill([
                                'locked_until' => null,
                                'login_attempts' => 0,
                            ])->save();
                        }

                        app(SecurityEventService::class)->record('otp.reset', 'info', auth()->user(), TenantContext::getTenant(), [
                            'target_id' => $record->getKey(),
                        ]);
                    }),
                Action::make('impersonate')
                    ->label('ورود به حساب')
                    ->icon('heroicon-o-identification')
                    ->visible(fn () => IamAuthorization::allows('iam.impersonate'))
                    ->form([
                        Select::make('tenant_id')
                            ->label('فضای کاری')
                            ->options(fn ($record) => $record->tenants()
                                ->get([
                                    'tenants.id as tenant_id',
                                    'tenants.name as tenant_name',
                                ])
                                ->pluck('tenant_name', 'tenant_id')
                                ->toArray())
                            ->searchable()
                            ->required(),
                        Textarea::make('reason')->label('دلیل')->required(),
                        TextInput::make('ticket_id')->label('شناسه تیکت')->required(),
                        TextInput::make('ttl_minutes')->label('مدت (دقیقه)')->numeric()->default(30)->minValue(5),
                        Toggle::make('restricted')->label('حالت مشاهده')->default(true),
                        TextInput::make('password')->label('رمز عبور')->password()->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('totp')->label('کد MFA')->dehydrated(fn ($state) => filled($state)),
                        TextInput::make('backup_code')->label('کد پشتیبان')->dehydrated(fn ($state) => filled($state)),
                    ])
                    ->action(function ($record, array $data) {
                        $tenant = Tenant::query()->find($data['tenant_id']);
                        if (! $tenant) {
                            return;
                        }

                        $actor = auth()->user();
                        if (! $actor) {
                            return;
                        }

                        $protectedAction = app(ProtectedActionService::class);
                        $requiresStepUp = in_array('iam.impersonate', (array) config('filamat-iam.protected_actions.require_mfa_actions', []), true);
                        if ($requiresStepUp) {
                            if (! empty($data['totp'])) {
                                $token = $protectedAction->issueWithTotp($actor, 'iam.impersonate', $data['totp'], $tenant);
                                $protectedAction->requireToken($actor, 'iam.impersonate', $tenant, $token);
                            } elseif (! empty($data['backup_code'])) {
                                $token = $protectedAction->issueWithBackupCode($actor, 'iam.impersonate', $data['backup_code'], $tenant);
                                $protectedAction->requireToken($actor, 'iam.impersonate', $tenant, $token);
                            } elseif (! empty($data['password'])) {
                                $token = $protectedAction->issueWithPassword($actor, 'iam.impersonate', $data['password'], $tenant);
                                $protectedAction->requireToken($actor, 'iam.impersonate', $tenant, $token);
                            } else {
                                throw new \RuntimeException('تایید هویت مجدد لازم است.');
                            }
                        }

                        app(ImpersonationService::class)->start(
                            $actor,
                            $record,
                            $tenant,
                            $data['reason'] ?? null,
                            $data['ticket_id'] ?? null,
                            isset($data['ttl_minutes']) ? (int) $data['ttl_minutes'] : null,
                            (bool) ($data['restricted'] ?? true)
                        );

                        return redirect()->to('/');
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        $relations = [
            UserTenantsRelationManager::class,
            UserGroupsRelationManager::class,
            UserPermissionOverridesRelationManager::class,
            UserWalletsRelationManager::class,
            UserApiKeysRelationManager::class,
            UserSessionsRelationManager::class,
            UserMfaMethodsRelationManager::class,
        ];

        $model = static::getModel();
        if (method_exists($model, 'tokens')) {
            $relations[] = UserTokensRelationManager::class;
        }

        return $relations;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
