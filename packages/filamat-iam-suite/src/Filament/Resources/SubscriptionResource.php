<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\SubscriptionResource\Pages\CreateSubscription;
use Filamat\IamSuite\Filament\Resources\SubscriptionResource\Pages\EditSubscription;
use Filamat\IamSuite\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Services\SubscriptionService;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'subscription';

    protected static ?string $model = Subscription::class;

    protected static ?string $navigationLabel = 'اشتراک‌ها';

    protected static ?string $pluralModelLabel = 'اشتراک‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string|\UnitEnum|null $navigationGroup = 'اشتراک';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('user_id')
                    ->label('کاربر (اختیاری)')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('plan_id')
                    ->label('پلن')
                    ->options(fn () => SubscriptionPlan::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'trialing' => 'آزمایشی',
                        'active' => 'فعال',
                        'past_due' => 'معوق',
                        'cancelled' => 'لغو شده',
                        'expired' => 'منقضی',
                    ])
                    ->required(),
                DateTimePicker::make('trial_ends_at')->label('پایان آزمایش')->nullable(),
                DateTimePicker::make('renews_at')->label('تمدید بعدی')->nullable(),
                DateTimePicker::make('ends_at')->label('پایان')->nullable(),
                KeyValue::make('meta')->label('متادیتا')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('plan.name')->label('پلن'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'trialing' => 'آزمایشی',
                        'active' => 'فعال',
                        'past_due' => 'معوق',
                        'cancelled' => 'لغو شده',
                        'expired' => 'منقضی',
                        default => $state,
                    }),
                TextColumn::make('renews_at')->label('تمدید'),
                TextColumn::make('ends_at')->label('پایان'),
            ])
            ->actions([
                Action::make('cancel')
                    ->label('لغو')
                    ->requiresConfirmation()
                    ->action(function (Subscription $record) {
                        app(SubscriptionService::class)->cancel($record, 'manual');
                    })
                    ->visible(fn (Subscription $record) => $record->status !== 'cancelled' && IamAuthorization::allows('subscription.manage')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptions::route('/'),
            'create' => CreateSubscription::route('/create'),
            'edit' => EditSubscription::route('/{record}/edit'),
        ];
    }
}
