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
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCampaignResource\Pages\CreateLoyaltyCampaign;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCampaignResource\Pages\EditLoyaltyCampaign;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyCampaignResource\Pages\ListLoyaltyCampaigns;
use Haida\FilamentLoyaltyClub\Models\LoyaltyCampaign;

class LoyaltyCampaignResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.campaign';

    protected static ?string $model = LoyaltyCampaign::class;

    protected static ?string $navigationLabel = 'کمپین‌ها';

    protected static ?string $pluralModelLabel = 'کمپین‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'active' => 'فعال',
                        'paused' => 'متوقف',
                        'completed' => 'تکمیل‌شده',
                    ])
                    ->default('draft'),
                Textarea::make('channels')->label('کانال‌ها (JSON)')->rows(2),
                Select::make('segment_strategy')
                    ->label('استراتژی بخش‌ها')
                    ->options(['all' => 'همه', 'any' => 'هرکدام'])
                    ->default('all'),
                DateTimePicker::make('schedule_start_at')->label('شروع زمان‌بندی')->nullable(),
                DateTimePicker::make('schedule_end_at')->label('پایان زمان‌بندی')->nullable(),
                Textarea::make('meta')->label('اطلاعات تکمیلی (JSON)')->rows(3)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('schedule_start_at')->label('شروع'),
                TextColumn::make('schedule_end_at')->label('پایان'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyCampaigns::route('/'),
            'create' => CreateLoyaltyCampaign::route('/create'),
            'edit' => EditLoyaltyCampaign::route('/{record}/edit'),
        ];
    }
}
