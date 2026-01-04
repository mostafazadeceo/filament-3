<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCouponResource\Pages\CreateLoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCouponResource\Pages\EditLoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCouponResource\Pages\ListLoyaltyCoupons;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCoupon;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;

class LoyaltyCouponResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.coupon';

    protected static ?string $model = LoyaltyCoupon::class;

    protected static ?string $navigationLabel = 'کوپن‌ها و ووچرها';

    protected static ?string $pluralModelLabel = 'کوپن‌ها و ووچرها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('code')->label('کد')->required()->maxLength(64),
                Select::make('type')
                    ->label('نوع')
                    ->options(['discount' => 'تخفیف', 'gift_card' => 'کارت هدیه', 'shipping' => 'ارسال رایگان'])
                    ->default('discount')
                    ->required(),
                Select::make('discount_type')
                    ->label('نوع تخفیف')
                    ->options(['percent' => 'درصدی', 'fixed' => 'مقداری'])
                    ->nullable(),
                TextInput::make('discount_value')->label('مقدار تخفیف')->numeric(),
                TextInput::make('max_uses')->label('حداکثر استفاده')->numeric(),
                TextInput::make('max_uses_per_customer')->label('حداکثر برای هر مشتری')->numeric(),
                Toggle::make('stackable')->label('قابل تجمیع')->inline(false),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['active' => 'فعال', 'inactive' => 'غیرفعال'])
                    ->default('active'),
                DateTimePicker::make('valid_from')->label('شروع اعتبار')->nullable(),
                DateTimePicker::make('valid_until')->label('پایان اعتبار')->nullable(),
                Select::make('issued_to_customer_id')
                    ->label('صادر شده برای')
                    ->options(fn () => LoyaltyCustomer::query()->pluck('phone', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable(),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('discount_value')->label('تخفیف'),
                TextColumn::make('used_count')->label('استفاده شده'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyCoupons::route('/'),
            'create' => CreateLoyaltyCoupon::route('/create'),
            'edit' => EditLoyaltyCoupon::route('/{record}/edit'),
        ];
    }
}
