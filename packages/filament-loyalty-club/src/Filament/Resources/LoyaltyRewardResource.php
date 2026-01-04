<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyRewardResource\Pages\CreateLoyaltyReward;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyRewardResource\Pages\EditLoyaltyReward;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyRewardResource\Pages\ListLoyaltyRewards;
use Haida\FilamentLoyaltyClub\Models\LoyaltyReward;

class LoyaltyRewardResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.reward';

    protected static ?string $model = LoyaltyReward::class;

    protected static ?string $navigationLabel = 'پاداش‌ها';

    protected static ?string $pluralModelLabel = 'پاداش‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'discount' => 'تخفیف',
                        'free_item' => 'کالای رایگان',
                        'shipping' => 'ارسال رایگان',
                        'experience' => 'تجربه',
                        'donation' => 'کمک خیریه',
                        'gift_card' => 'کارت هدیه',
                    ])
                    ->required(),
                Textarea::make('description')->label('توضیحات')->rows(3),
                TextInput::make('points_cost')->label('هزینه امتیاز')->numeric()->default(0),
                TextInput::make('cashback_cost')->label('هزینه کش‌بک')->numeric()->default(0),
                TextInput::make('inventory')->label('موجودی')->numeric(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['active' => 'فعال', 'inactive' => 'غیرفعال'])
                    ->default('active'),
                DateTimePicker::make('valid_from')->label('شروع اعتبار')->nullable(),
                DateTimePicker::make('valid_until')->label('پایان اعتبار')->nullable(),
                Textarea::make('constraints')->label('محدودیت‌ها (JSON)')->rows(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('points_cost')->label('امتیاز'),
                TextColumn::make('cashback_cost')->label('کش‌بک'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyRewards::route('/'),
            'create' => CreateLoyaltyReward::route('/create'),
            'edit' => EditLoyaltyReward::route('/{record}/edit'),
        ];
    }
}
