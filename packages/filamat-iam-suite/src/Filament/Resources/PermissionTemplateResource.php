<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\PermissionTemplateResource\Pages\CreatePermissionTemplate;
use Filamat\IamSuite\Filament\Resources\PermissionTemplateResource\Pages\EditPermissionTemplate;
use Filamat\IamSuite\Filament\Resources\PermissionTemplateResource\Pages\ListPermissionTemplates;
use Filamat\IamSuite\Models\PermissionTemplate;
use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionTemplateResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = PermissionTemplate::class;

    protected static ?string $navigationLabel = 'قالب‌های دسترسی';

    protected static ?string $pluralModelLabel = 'قالب‌های دسترسی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(required: false)->label('مالک قالب (اختیاری)'),
                TextInput::make('name')->label('نام قالب')->required(),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'role' => 'قالب نقش',
                        'permission' => 'قالب مجوز',
                    ])
                    ->required(),
                Select::make('permissions')
                    ->label('لیست مجوزها')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => AccessSettings::permissionOptions(TenantContext::getTenant()))
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'role' => 'نقش',
                        'permission' => 'مجوز',
                        default => $state,
                    }),
                TextColumn::make('tenant_id')->label('مالک'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                Action::make('apply')
                    ->label('ایجاد از قالب')
                    ->form([
                        Select::make('tenant_id')
                            ->label('فضای کاری')
                            ->options(fn () => \Filamat\IamSuite\Models\Tenant::query()->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required()
                            ->default(fn () => TenantContext::getTenantId())
                            ->visible(fn () => TenantContext::shouldBypass())
                            ->dehydrateStateUsing(fn ($state) => TenantContext::shouldBypass() ? $state : TenantContext::getTenantId()),
                        TextInput::make('name')
                            ->label('نام مقصد')
                            ->helperText('در صورت خالی بودن، نام قالب استفاده می‌شود.')
                            ->nullable(),
                    ])
                    ->action(function (PermissionTemplate $record, array $data) {
                        $tenantId = TenantContext::shouldBypass() ? ($data['tenant_id'] ?? null) : TenantContext::getTenantId();
                        if (! $tenantId) {
                            return;
                        }

                        app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);

                        $permissions = array_filter($record->permissions ?? []);
                        $guard = 'web';

                        $resolvedPermissions = collect($permissions)->map(function (string $permission) use ($tenantId, $guard) {
                            return Permission::query()->firstOrCreate([
                                'name' => $permission,
                                'guard_name' => $guard,
                                'tenant_id' => $tenantId,
                            ]);
                        });

                        if ($record->type === 'role') {
                            $roleName = $data['name'] ?: $record->name;
                            $role = Role::query()->firstOrCreate([
                                'name' => $roleName,
                                'guard_name' => $guard,
                                'tenant_id' => $tenantId,
                            ]);
                            $role->syncPermissions($resolvedPermissions);
                        } else {
                            $resolvedPermissions->each->save();
                        }

                        app(PermissionRegistrar::class)->forgetCachedPermissions();

                        app(AuditService::class)->log('permission_template.applied', $record, [
                            'tenant_id' => $tenantId,
                            'type' => $record->type,
                            'name' => $data['name'] ?: $record->name,
                        ]);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermissionTemplates::route('/'),
            'create' => CreatePermissionTemplate::route('/create'),
            'edit' => EditPermissionTemplate::route('/{record}/edit'),
        ];
    }
}
