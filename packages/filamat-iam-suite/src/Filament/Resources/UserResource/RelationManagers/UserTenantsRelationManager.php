<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserTenantsRelationManager extends RelationManager
{
    protected static string $relationship = 'tenants';

    protected static ?string $title = 'فضاهای کاری';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('pivot.role')
                    ->label('نقش')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'owner' => 'مالک',
                        'admin' => 'مدیر',
                        'member' => 'عضو',
                        default => $state,
                    }),
                TextColumn::make('pivot.status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'invited' => 'دعوت شده',
                        'inactive' => 'غیرفعال',
                        default => $state,
                    }),
                TextColumn::make('pivot.joined_at')->label('تاریخ عضویت'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('افزودن')
                    ->visible(fn () => IamAuthorization::allows('iam.manage'))
                    ->recordSelectOptionsQuery(function ($query) {
                        $tenantId = TenantContext::getTenantId();
                        if ($tenantId && ! TenantContext::shouldBypass()) {
                            return $query->where('id', $tenantId);
                        }

                        return $query;
                    })
                    ->form([
                        Select::make('role')
                            ->label('نقش')
                            ->options([
                                'owner' => 'مالک',
                                'admin' => 'مدیر',
                                'member' => 'عضو',
                            ])
                            ->required(),
                        Select::make('status')
                            ->label('وضعیت')
                            ->options([
                                'active' => 'فعال',
                                'invited' => 'دعوت شده',
                                'inactive' => 'غیرفعال',
                            ])
                            ->default('active')
                            ->required(),
                        DateTimePicker::make('joined_at')->label('تاریخ عضویت')->nullable(),
                    ])
                    ->after(function (AttachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();
                        $data = $action->getData();

                        app(AuditService::class)->log('tenant.user.attached', $record, [
                            'user_id' => $owner?->getKey(),
                            'role' => $data['role'] ?? 'member',
                            'status' => $data['status'] ?? 'active',
                        ]);
                    }),
            ])
            ->actions([
                Action::make('activate')
                    ->label('فعال‌سازی')
                    ->visible(fn ($record) => IamAuthorization::allows('iam.manage') && ($record->pivot->status ?? null) !== 'active')
                    ->action(function ($record) {
                        $record->pivot->update(['status' => 'active']);
                        app(AuditService::class)->log('tenant.user.activated', $record, [
                            'user_id' => $this->getOwnerRecord()?->getKey(),
                        ]);
                    }),
                Action::make('deactivate')
                    ->label('غیرفعال‌سازی')
                    ->visible(fn ($record) => IamAuthorization::allows('iam.manage') && ($record->pivot->status ?? null) === 'active')
                    ->action(function ($record) {
                        $record->pivot->update(['status' => 'inactive']);
                        app(AuditService::class)->log('tenant.user.deactivated', $record, [
                            'user_id' => $this->getOwnerRecord()?->getKey(),
                        ]);
                    }),
                DetachAction::make()
                    ->label('حذف عضویت')
                    ->visible(fn () => IamAuthorization::allows('iam.manage'))
                    ->after(function (DetachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();

                        app(AuditService::class)->log('tenant.user.detached', $record, [
                            'user_id' => $owner?->getKey(),
                        ]);
                    }),
            ]);
    }
}
