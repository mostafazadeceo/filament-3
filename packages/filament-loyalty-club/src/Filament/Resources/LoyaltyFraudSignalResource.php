<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyFraudSignalResource\Pages\EditLoyaltyFraudSignal;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltyFraudSignalResource\Pages\ListLoyaltyFraudSignals;
use Haida\FilamentLoyaltyClub\Models\LoyaltyFraudSignal;

class LoyaltyFraudSignalResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.fraud';

    protected static ?string $model = LoyaltyFraudSignal::class;

    protected static ?string $navigationLabel = 'صندوق تخلف';

    protected static ?string $pluralModelLabel = 'صندوق تخلف';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('type')->label('نوع')->disabled(),
                Select::make('severity')
                    ->label('شدت')
                    ->options(['low' => 'کم', 'medium' => 'متوسط', 'high' => 'بالا'])
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['open' => 'باز', 'reviewed' => 'بررسی شده', 'closed' => 'بسته'])
                    ->required(),
                TextInput::make('score')->label('امتیاز')->numeric(),
                TextInput::make('resolution')->label('نتیجه'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('severity')->label('شدت'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('score')->label('امتیاز'),
                TextColumn::make('detected_at')->label('تاریخ تشخیص'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltyFraudSignals::route('/'),
            'edit' => EditLoyaltyFraudSignal::route('/{record}/edit'),
        ];
    }
}
