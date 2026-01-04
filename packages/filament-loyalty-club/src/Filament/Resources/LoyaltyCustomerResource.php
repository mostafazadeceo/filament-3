<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCustomerResource\Pages\CreateLoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCustomerResource\Pages\EditLoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCustomerResource\Pages\ListLoyaltyCustomers;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCustomerResource\RelationManagers\LoyaltyWalletLedgersRelationManager;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCustomer;
use Haida\FilamentLoyaltyClub\Models\LoyaltyTier;
use Illuminate\Database\Eloquent\Builder;

class LoyaltyCustomerResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.customer';

    protected static ?string $model = LoyaltyCustomer::class;

    protected static ?string $navigationLabel = 'مشتریان';

    protected static ?string $pluralModelLabel = 'مشتریان';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('first_name')->label('نام')->maxLength(255),
                TextInput::make('last_name')->label('نام خانوادگی')->maxLength(255),
                TextInput::make('phone')->label('موبایل')->maxLength(20),
                TextInput::make('email')->label('ایمیل')->maxLength(255),
                Select::make('tier_id')
                    ->label('سطح')
                    ->options(fn () => LoyaltyTier::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                DatePicker::make('birth_date')->label('تاریخ تولد')->nullable(),
                Toggle::make('marketing_opt_in')->label('رضایت بازاریابی')->inline(false),
                Toggle::make('sms_opt_in')->label('رضایت پیامک')->inline(false),
                Toggle::make('whatsapp_opt_in')->label('رضایت واتساپ')->inline(false),
                Toggle::make('telegram_opt_in')->label('رضایت تلگرام')->inline(false),
                Toggle::make('bale_opt_in')->label('رضایت بله')->inline(false),
                Toggle::make('webpush_opt_in')->label('رضایت وب‌پوش')->inline(false),
                Toggle::make('email_opt_in')->label('رضایت ایمیل')->inline(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->label('نام')->searchable(),
                TextColumn::make('last_name')->label('نام خانوادگی')->searchable(),
                TextColumn::make('phone')->label('موبایل')->searchable(),
                TextColumn::make('email')->label('ایمیل')->searchable(),
                TextColumn::make('tier.name')->label('سطح'),
                TextColumn::make('walletAccount.points_balance')->label('امتیاز'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => $state === 'active' ? 'فعال' : 'غیرفعال'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ]),
                SelectFilter::make('tier_id')
                    ->label('سطح')
                    ->options(fn () => LoyaltyTier::query()->pluck('name', 'id')->toArray()),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['tier', 'walletAccount']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyCustomers::route('/'),
            'create' => CreateLoyaltyCustomer::route('/create'),
            'edit' => EditLoyaltyCustomer::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            LoyaltyWalletLedgersRelationManager::class,
        ];
    }
}
