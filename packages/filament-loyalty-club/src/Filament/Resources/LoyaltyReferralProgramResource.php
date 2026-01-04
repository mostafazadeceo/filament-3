<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyReferralProgramResource\Pages\CreateLoyaltyReferralProgram;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyReferralProgramResource\Pages\EditLoyaltyReferralProgram;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyReferralProgramResource\Pages\ListLoyaltyReferralPrograms;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReferralProgram;

class LoyaltyReferralProgramResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.referral.program';

    protected static ?string $model = LoyaltyReferralProgram::class;

    protected static ?string $navigationLabel = 'برنامه‌های معرفی';

    protected static ?string $pluralModelLabel = 'برنامه‌های معرفی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                TextInput::make('code_prefix')->label('پیشوند کد')->maxLength(32),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['active' => 'فعال', 'inactive' => 'غیرفعال'])
                    ->default('active'),
                TextInput::make('qualification_event')->label('رویداد معیار')->maxLength(64),
                TextInput::make('min_purchase_amount')->label('حداقل مبلغ')->numeric(),
                TextInput::make('waiting_days')->label('روز انتظار')->numeric()->default(14),
                TextInput::make('max_per_referrer')->label('سقف برای معرف')->numeric(),
                TextInput::make('period_days')->label('بازه محدودیت')->numeric(),
                Select::make('reward_type')
                    ->label('نوع پاداش')
                    ->options(['points' => 'امتیاز', 'cashback' => 'کش‌بک'])
                    ->default('points'),
                TextInput::make('referrer_points')->label('امتیاز معرف')->numeric()->default(0),
                TextInput::make('referee_points')->label('امتیاز معرفی‌شده')->numeric()->default(0),
                TextInput::make('referrer_cashback')->label('کش‌بک معرف')->numeric()->default(0),
                TextInput::make('referee_cashback')->label('کش‌بک معرفی‌شده')->numeric()->default(0),
                DateTimePicker::make('valid_from')->label('شروع اعتبار')->nullable(),
                DateTimePicker::make('valid_until')->label('پایان اعتبار')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('waiting_days')->label('روز انتظار'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyReferralPrograms::route('/'),
            'create' => CreateLoyaltyReferralProgram::route('/create'),
            'edit' => EditLoyaltyReferralProgram::route('/{record}/edit'),
        ];
    }
}
