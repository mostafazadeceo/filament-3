<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\NotificationResource\Pages\CreateNotification;
use Filamat\IamSuite\Filament\Resources\NotificationResource\Pages\ListNotifications;
use Filamat\IamSuite\Jobs\SendNotificationJob;
use Filamat\IamSuite\Models\Notification;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'notification';

    protected static ?string $model = Notification::class;

    protected static ?string $navigationLabel = 'اعلان‌ها';

    protected static ?string $pluralModelLabel = 'اعلان‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static string|\UnitEnum|null $navigationGroup = 'اعلان‌ها';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery());
    }

    protected static function permissionMap(): array
    {
        return [
            'viewAny' => 'notification.view',
            'view' => 'notification.view',
            'create' => 'notification.send',
            'update' => 'notification.send',
            'delete' => 'notification.send',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        $userModel = config('auth.providers.users.model');

        return $schema
            ->schema([
                static::tenantSelect(required: false),
                Select::make('user_id')
                    ->label('کاربر (اختیاری)')
                    ->options(function () use ($userModel): array {
                        $query = $userModel::query();
                        $tenantId = TenantContext::getTenantId();
                        if ($tenantId && method_exists($userModel, 'tenants')) {
                            $query->whereHas('tenants', function (Builder $builder) use ($tenantId) {
                                $builder->where('tenants.id', $tenantId);
                            });
                        }

                        return $query->pluck('name', 'id')->toArray();
                    })
                    ->searchable()
                    ->nullable(),
                TextInput::make('type')
                    ->label('نوع اعلان')
                    ->default('custom')
                    ->required(),
                KeyValue::make('payload')
                    ->label('جزئیات')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'queued' => 'در صف',
                        'sent' => 'ارسال شده',
                        'failed' => 'ناموفق',
                        'skipped' => 'رد شده',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                Action::make('resend')
                    ->label('ارسال مجدد')
                    ->requiresConfirmation()
                    ->visible(fn () => IamAuthorization::allows('notification.send'))
                    ->action(function (Notification $record) {
                        $record->update(['status' => 'queued']);
                        SendNotificationJob::dispatch($record->getKey());
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
            'create' => CreateNotification::route('/create'),
        ];
    }
}
