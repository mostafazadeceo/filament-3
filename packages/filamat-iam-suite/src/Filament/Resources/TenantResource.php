<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\TenantResource\Pages\CreateTenant;
use Filamat\IamSuite\Filament\Resources\TenantResource\Pages\EditTenant;
use Filamat\IamSuite\Filament\Resources\TenantResource\Pages\ListTenants;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\ModuleCatalog;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TenantResource extends IamResource
{
    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = Tenant::class;

    protected static ?string $navigationLabel = 'فضاهای کاری';

    protected static ?string $pluralModelLabel = 'فضاهای کاری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت کلان';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('نام')->required(),
                TextInput::make('slug')->label('شناسه')->required(),
                Select::make('organization_id')
                    ->label('سازمان')
                    ->options(fn () => Organization::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('owner_user_id')
                    ->label('مالک')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->required(),
                TextInput::make('locale')->label('زبان')->nullable(),
                TextInput::make('timezone')->label('منطقه زمانی')->nullable(),
                TextInput::make('settings.brand_name')->label('نام برند')->nullable(),
                TextInput::make('settings.logo_url')->label('آدرس لوگو')->nullable(),
                TextInput::make('settings.primary_color')->label('رنگ اصلی')->nullable(),
                TextInput::make('settings.allowed_features')
                    ->label('ویژگی‌های مجاز (جداشده با ویرگول)')
                    ->helperText('مثال فنی: wallet, subscriptions, notifications')
                    ->nullable(),
                CheckboxList::make('settings.access.modules')
                    ->label('ماژول‌های فعال')
                    ->options(fn () => app(ModuleCatalog::class)->moduleOptions())
                    ->columns(2)
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('slug')->label('شناسه')->searchable(),
                TextColumn::make('organization.name')->label('سازمان'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        default => $state,
                    }),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenants::route('/'),
            'create' => CreateTenant::route('/create'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
