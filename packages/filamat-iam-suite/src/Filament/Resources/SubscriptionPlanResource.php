<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\SubscriptionPlanResource\Pages\CreateSubscriptionPlan;
use Filamat\IamSuite\Filament\Resources\SubscriptionPlanResource\Pages\EditSubscriptionPlan;
use Filamat\IamSuite\Filament\Resources\SubscriptionPlanResource\Pages\ListSubscriptionPlans;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionPlanResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'subscription';

    protected static ?string $model = SubscriptionPlan::class;

    protected static ?string $navigationLabel = 'پلن‌ها';

    protected static ?string $pluralModelLabel = 'پلن‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'اشتراک';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(required: false)->label('مالک پلن (اختیاری)'),
                Select::make('package_profile')
                    ->label('پروفایل پکیج')
                    ->options(fn () => AccessSettings::packageOptions(TenantContext::getTenant()))
                    ->searchable()
                    ->reactive()
                    ->dehydrated(false)
                    ->visible(fn () => AccessSettings::packageOptions(TenantContext::getTenant()) !== [])
                    ->afterStateUpdated(function ($state, callable $set) {
                        $package = AccessSettings::findPackage(TenantContext::getTenant(), (string) $state);
                        if (! $package) {
                            return;
                        }

                        $set('features.permissions', $package['permissions'] ?? []);
                        $set('features.flags', $package['features'] ?? []);
                        $set('features.quotas', $package['quotas'] ?? []);
                    }),
                TextInput::make('name')->label('نام')->required(),
                TextInput::make('code')->label('کد')->required(),
                TextInput::make('price')->label('قیمت')->numeric(),
                TextInput::make('currency')->label('ارز')->default('irr'),
                TextInput::make('period_days')->label('دوره (روز)')->numeric()->default(30),
                TextInput::make('trial_days')->label('روز آزمایشی')->numeric()->default(0),
                TextInput::make('seat_limit')->label('حداکثر صندلی')->numeric()->nullable(),
                TextInput::make('storage_limit')->label('حداکثر فضا (مگابایت)')->numeric()->nullable(),
                TextInput::make('module_limit')->label('حداکثر ماژول')->numeric()->nullable(),
                Select::make('features.permissions')
                    ->label('مجوزهای مجاز')
                    ->multiple()
                    ->searchable()
                    ->options(fn () => AccessSettings::permissionOptions(TenantContext::getTenant())),
                KeyValue::make('features.flags')->label('ویژگی‌ها')->nullable(),
                KeyValue::make('features.quotas')->label('کوتاها')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('code')->label('کد'),
                TextColumn::make('tenant.name')->label('مالک'),
                TextColumn::make('price')->label('قیمت')->numeric(),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('period_days')->label('دوره'),
                TextColumn::make('created_at')->label('ایجاد'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubscriptionPlans::route('/'),
            'create' => CreateSubscriptionPlan::route('/create'),
            'edit' => EditSubscriptionPlan::route('/{record}/edit'),
        ];
    }
}
