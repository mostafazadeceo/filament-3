<?php

namespace Haida\FilamentLoyaltyClub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltySegmentResource\Pages\CreateLoyaltySegment;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltySegmentResource\Pages\EditLoyaltySegment;
use Haida\FilamentLoyaltyClub\Filament\Resources\LoyaltySegmentResource\Pages\ListLoyaltySegments;
use Haida\FilamentLoyaltyClub\Models\LoyaltySegment;

class LoyaltySegmentResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'loyalty.segment';

    protected static ?string $model = LoyaltySegment::class;

    protected static ?string $navigationLabel = 'بخش‌بندی';

    protected static ?string $pluralModelLabel = 'بخش‌بندی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-funnel';

    protected static string|\UnitEnum|null $navigationGroup = 'باشگاه مشتریان';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options(['rule' => 'قانونی', 'rfm' => 'RFM'])
                    ->default('rule')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(['active' => 'فعال', 'inactive' => 'غیرفعال'])
                    ->default('active'),
                Textarea::make('rules')->label('قوانین (JSON)')->rows(4)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('status')->label('وضعیت'),
                TextColumn::make('last_built_at')->label('آخرین ساخت'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoyaltySegments::route('/'),
            'create' => CreateLoyaltySegment::route('/create'),
            'edit' => EditLoyaltySegment::route('/{record}/edit'),
        ];
    }
}
