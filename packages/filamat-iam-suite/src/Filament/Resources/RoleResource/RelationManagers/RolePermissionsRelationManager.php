<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\RoleResource\RelationManagers;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\PermissionLabels;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RolePermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $title = 'مجوزها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('مجوز')
                    ->getStateUsing(fn ($record) => PermissionLabels::label($record->name)),
                TextColumn::make('name')->label('کلید')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('guard_name')->label('گارد'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('افزودن')
                    ->visible(fn () => IamAuthorization::allows('iam.manage'))
                    ->recordTitle(fn ($record) => PermissionLabels::labelWithKey($record->name))
                    ->recordTitleAttribute('name')
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $owner = $this->getOwnerRecord();
                        $tenantId = $owner->tenant_id ?? null;
                        $tenant = $tenantId ? Tenant::query()->find($tenantId) : null;

                        if (! $tenantId) {
                            $query->whereNull('tenant_id');
                        } else {
                            $query->where(function (Builder $query) use ($tenantId) {
                                $query->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
                            });
                        }

                        if (AccessSettings::companyEnforced($tenant)) {
                            $allowed = AccessSettings::companyAllowedPermissions($tenant);
                            if ($allowed !== []) {
                                $query->whereIn('name', $allowed);
                            }
                        }

                        return $query;
                    })
                    ->after(function (AttachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();

                        app(AuditService::class)->log('role.permission.attached', $owner, [
                            'permission_id' => $record?->getKey(),
                        ]);
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('حذف')
                    ->visible(fn () => IamAuthorization::allows('iam.manage'))
                    ->after(function (DetachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();

                        app(AuditService::class)->log('role.permission.detached', $owner, [
                            'permission_id' => $record?->getKey(),
                        ]);
                    }),
            ]);
    }
}
