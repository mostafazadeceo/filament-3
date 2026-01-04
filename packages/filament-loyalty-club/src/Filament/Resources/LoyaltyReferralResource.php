<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyReferralResource\Pages\EditLoyaltyReferral;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyReferralResource\Pages\ListLoyaltyReferrals;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferral;

class LoyaltyReferralResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.referral';

    protected static ?string $model = LoyaltyReferral::class;

    protected static ?string $navigationLabel = 'معرفی‌ها';

    protected static ?string $pluralModelLabel = 'معرفی‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('referral_code')->label('کد معرفی')->disabled(),
                TextInput::make('referrer_customer_id')->label('شناسه معرف')->numeric()->disabled(),
                TextInput::make('referee_customer_id')->label('شناسه معرفی‌شده')->numeric()->disabled(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'qualified' => 'تایید شده',
                        'rewarded' => 'پاداش داده شده',
                        'flagged' => 'پرچم‌گذاری',
                    ])
                    ->required(),
                TextInput::make('fraud_score')->label('امتیاز ریسک')->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('referral_code')->label('کد')->searchable(),
                TextColumn::make('referrer_customer_id')->label('معرف'),
                TextColumn::make('referee_customer_id')->label('معرفی‌شده'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('reward_due_at')->label('سررسید پاداش'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyReferrals::route('/'),
            'edit' => EditLoyaltyReferral::route('/{record}/edit'),
        ];
    }
}
