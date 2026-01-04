<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\GroupResource\RelationManagers;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\PermissionLabels;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class GroupPermissionsRelationManager extends RelationManager
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
                TextColumn::make('pivot.effect')
                    ->label('اثر')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'allow' => 'اجازه',
                        'deny' => 'عدم اجازه',
                        default => $state,
                    }),
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
                    ->form([
                        Select::make('effect')
                            ->label('اثر')
                            ->options([
                                'allow' => 'اجازه',
                                'deny' => 'عدم اجازه',
                            ])
                            ->default('allow')
                            ->required(),
                        Textarea::make('reason')->label('دلیل')->required(),
                    ])
                    ->after(function (AttachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();
                        $data = $action->getData();

                        app(AuditService::class)->log('group.permission.attached', $owner, [
                            'permission_id' => $record?->getKey(),
                            'effect' => $data['effect'] ?? 'allow',
                            'reason' => $data['reason'] ?? null,
                        ]);
                    }),
            ])
            ->actions([
                DetachAction::make()
                    ->label('حذف')
                    ->visible(fn () => IamAuthorization::allows('iam.manage'))
                    ->form([
                        Textarea::make('reason')->label('دلیل')->required(),
                    ])
                    ->after(function (DetachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();
                        $data = $action->getData();

                        app(AuditService::class)->log('group.permission.detached', $owner, [
                            'permission_id' => $record?->getKey(),
                            'reason' => $data['reason'] ?? null,
                        ]);
                    }),
            ]);
    }
}
