<?php

namespace Haida\FilamentCommerceExperience\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceBuyNowPreferenceResource\Pages\CreateExperienceBuyNowPreference;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceBuyNowPreferenceResource\Pages\EditExperienceBuyNowPreference;
use Haida\FilamentCommerceExperience\Filament\Resources\ExperienceBuyNowPreferenceResource\Pages\ListExperienceBuyNowPreferences;
use Haida\FilamentCommerceExperience\Models\ExperienceBuyNowPreference;
use Illuminate\Database\Eloquent\Model;

class ExperienceBuyNowPreferenceResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = ExperienceBuyNowPreference::class;

    protected static ?string $modelLabel = 'خرید فوری';

    protected static ?string $pluralModelLabel = 'خرید فوری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static string|\UnitEnum|null $navigationGroup = 'تجربه مشتری';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage');
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('experience.buy_now.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('customer_id')
                    ->label('شناسه مشتری')
                    ->numeric()
                    ->required(),
                TextInput::make('default_address_id')
                    ->label('شناسه آدرس')
                    ->numeric()
                    ->nullable(),
                TextInput::make('default_payment_provider')
                    ->label('درگاه پیش‌فرض')
                    ->maxLength(64)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'revoked' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                Toggle::make('requires_2fa')
                    ->label('نیاز به ۲FA')
                    ->default(false),
                DateTimePicker::make('consent_at')
                    ->label('تاریخ رضایت')
                    ->nullable(),
                TextInput::make('consent_ip')
                    ->label('IP رضایت')
                    ->maxLength(64)
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_id')
                    ->label('مشتری'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('default_payment_provider')
                    ->label('درگاه'),
                TextColumn::make('consent_at')
                    ->label('رضایت')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceBuyNowPreferences::route('/'),
            'create' => CreateExperienceBuyNowPreference::route('/create'),
            'edit' => EditExperienceBuyNowPreference::route('/{record}/edit'),
        ];
    }
}
