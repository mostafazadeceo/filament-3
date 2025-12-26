<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\GroupResource\RelationManagers;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GroupRolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $title = 'نقش‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام نقش'),
                TextColumn::make('guard_name')->label('گارد'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('افزودن')
                    ->visible(fn () => IamAuthorization::allows('iam.manage'))
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
                            $allowed = AccessSettings::companyAllowedRoles($tenant);
                            if ($allowed !== []) {
                                $query->whereIn('name', $allowed);
                            }
                        }

                        return $query;
                    })
                    ->after(function (AttachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();

                        app(AuditService::class)->log('group.role.attached', $owner, [
                            'role_id' => $record?->getKey(),
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

                        app(AuditService::class)->log('group.role.detached', $owner, [
                            'role_id' => $record?->getKey(),
                        ]);
                    }),
            ]);
    }
}
