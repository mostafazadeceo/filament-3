<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources\UserResource\RelationManagers;

use Filamat\IamSuite\Services\AuditService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserGroupsRelationManager extends RelationManager
{
    protected static string $relationship = 'groups';

    protected static ?string $title = 'گروه‌ها';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('description')->label('توضیح')->limit(30),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('افزودن')
                    ->visible(fn () => IamAuthorization::allows('iam.manage'))
                    ->after(function (AttachAction $action) {
                        $owner = $this->getOwnerRecord();
                        $record = $action->getRecord();

                        app(AuditService::class)->log('user.group.attached', $owner, [
                            'group_id' => $record?->getKey(),
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

                        app(AuditService::class)->log('user.group.detached', $owner, [
                            'group_id' => $record?->getKey(),
                        ]);
                    }),
            ]);
    }
}
