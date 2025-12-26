<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\ApiKeyResource\Pages\CreateApiKey;
use Filamat\IamSuite\Filament\Resources\ApiKeyResource\Pages\EditApiKey;
use Filamat\IamSuite\Filament\Resources\ApiKeyResource\Pages\ListApiKeys;
use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Support\AccessSettings;
use Filamat\IamSuite\Support\PermissionsPresenter;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ApiKeyResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'api';

    protected static ?string $model = ApiKey::class;

    protected static ?string $navigationLabel = 'کلیدهای ای‌پی‌آی';

    protected static ?string $pluralModelLabel = 'کلیدهای ای‌پی‌آی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('نام کلید')->required(),
                static::tenantSelect(required: false),
                Select::make('user_id')
                    ->label('کاربر (اختیاری)')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TagsInput::make('abilities')
                    ->label('سطوح دسترسی')
                    ->separator(',')
                    ->suggestions(function () {
                        $options = AccessSettings::permissionOptions(TenantContext::getTenant());
                        $suggestions = [];

                        foreach ($options as $group) {
                            foreach ($group as $label) {
                                $suggestions[] = $label;
                            }
                        }

                        return $suggestions;
                    })
                    ->formatStateUsing(fn ($state) => PermissionsPresenter::listWithLabels((array) $state))
                    ->dehydrateStateUsing(fn ($state) => PermissionsPresenter::normalizeList((array) $state))
                    ->nullable(),
                DateTimePicker::make('expires_at')->label('تاریخ انقضا')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام'),
                TextColumn::make('token_prefix')->label('پیشوند کلید'),
                TextColumn::make('tenant.name')->label('فضای کاری'),
                TextColumn::make('user.name')->label('کاربر'),
                TextColumn::make('last_used_at')->label('آخرین استفاده'),
                TextColumn::make('expires_at')->label('انقضا'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListApiKeys::route('/'),
            'create' => CreateApiKey::route('/create'),
            'edit' => EditApiKey::route('/{record}/edit'),
        ];
    }
}
