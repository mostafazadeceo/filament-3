<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\PrivilegeRequestResource\Pages\CreatePrivilegeRequest;
use Filamat\IamSuite\Filament\Resources\PrivilegeRequestResource\Pages\ListPrivilegeRequests;
use Filamat\IamSuite\Models\PrivilegeRequest;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrivilegeRequestResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'pam';

    protected static ?string $model = PrivilegeRequest::class;

    protected static ?string $navigationLabel = 'درخواست‌های نقش ممتاز';

    protected static ?string $pluralModelLabel = 'درخواست‌های نقش ممتاز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت دسترسی';

    public static function form(Schema $schema): Schema
    {
        $userModel = config('auth.providers.users.model');

        return $schema->schema([
            static::tenantSelect(),
            Select::make('user_id')
                ->label('کاربر')
                ->options(fn () => $userModel::query()->pluck('name', 'id')->toArray())
                ->searchable()
                ->required(),
            Select::make('role_id')
                ->label('نقش')
                ->relationship('role', 'name')
                ->searchable()
                ->required(),
            TextInput::make('ticket_id')->label('شناسه تیکت')->required(),
            Textarea::make('reason')->label('دلیل')->required(),
            TextInput::make('requested_duration_minutes')->label('مدت (دقیقه)')->numeric()->required(),
            DateTimePicker::make('request_expires_at')->label('انقضای درخواست')->nullable(),
        ]);
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allowsAny(['pam.request', 'pam.manage']);
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
                        'pending' => 'در انتظار',
                        'approved' => 'تایید شده',
                        'denied' => 'رد شده',
                        'expired' => 'منقضی شده',
                        default => $state,
                    }),
                TextColumn::make('requested_duration_minutes')->label('مدت'),
                TextColumn::make('requestedBy.name')->label('درخواست کننده'),
                TextColumn::make('decidedBy.name')->label('تصمیم گیرنده'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                Action::make('approve')
                    ->label('تایید')
                    ->visible(fn (PrivilegeRequest $record) => $record->status === 'pending' && IamAuthorization::allows('pam.approve'))
                    ->form([
                        Textarea::make('note')->label('یادداشت')->required(),
                    ])
                    ->action(function (PrivilegeRequest $record, array $data) {
                        app(\Filamat\IamSuite\Services\PrivilegeElevationService::class)
                            ->approve($record, auth()->user(), $data['note'] ?? null);
                    }),
                Action::make('deny')
                    ->label('رد')
                    ->color('danger')
                    ->visible(fn (PrivilegeRequest $record) => $record->status === 'pending' && IamAuthorization::allows('pam.approve'))
                    ->form([
                        Textarea::make('note')->label('یادداشت')->required(),
                    ])
                    ->action(function (PrivilegeRequest $record, array $data) {
                        app(\Filamat\IamSuite\Services\PrivilegeElevationService::class)
                            ->deny($record, auth()->user(), $data['note'] ?? null);
                    }),
                Action::make('activate')
                    ->label('فعال‌سازی')
                    ->visible(fn (PrivilegeRequest $record) => $record->status === 'approved' && IamAuthorization::allows('pam.activate'))
                    ->form([
                        DateTimePicker::make('expires_at')->label('انقضا')->nullable(),
                    ])
                    ->action(function (PrivilegeRequest $record, array $data) {
                        app(\Filamat\IamSuite\Services\PrivilegeElevationService::class)->activate(
                            $record->tenant,
                            $record->user,
                            $record->role,
                            $record,
                            auth()->user(),
                            $record->reason,
                            $record->ticket_id,
                            $data['expires_at'] ?? null
                        );
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrivilegeRequests::route('/'),
            'create' => CreatePrivilegeRequest::route('/create'),
        ];
    }
}
