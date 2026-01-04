<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\PrivilegeActivationResource\Pages\ListPrivilegeActivations;
use Filamat\IamSuite\Models\PrivilegeActivation;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrivilegeActivationResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'pam';

    protected static ?string $model = PrivilegeActivation::class;

    protected static ?string $navigationLabel = 'فعال‌سازی نقش ممتاز';

    protected static ?string $pluralModelLabel = 'فعال‌سازی نقش ممتاز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('role.name')->label('نقش'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'revoked' => 'لغو شده',
                        'expired' => 'منقضی شده',
                        default => $state,
                    }),
                TextColumn::make('expires_at')->label('انقضا'),
                TextColumn::make('activated_at')->label('فعال‌سازی'),
                TextColumn::make('revoked_at')->label('لغو'),
            ])
            ->actions([
                Action::make('revoke')
                    ->label('لغو')
                    ->color('danger')
                    ->visible(fn (PrivilegeActivation $record) => $record->status === 'active' && IamAuthorization::allows('pam.revoke'))
                    ->form([
                        Textarea::make('reason')->label('دلیل')->required(),
                    ])
                    ->action(function (PrivilegeActivation $record, array $data) {
                        app(\Filamat\IamSuite\Services\PrivilegeElevationService::class)
                            ->revoke($record, auth()->user(), $data['reason'] ?? null);
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrivilegeActivations::route('/'),
        ];
    }
}
