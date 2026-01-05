<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages\CreateOrganization;
use Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages\EditOrganization;
use Filamat\IamSuite\Filament\Resources\OrganizationResource\Pages\ListOrganizations;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Services\ModuleCatalog;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationResource extends IamResource
{
    protected static ?string $permissionPrefix = 'iam';

    protected static ?string $model = Organization::class;

    protected static ?string $navigationLabel = 'سازمان‌ها';

    protected static ?string $pluralModelLabel = 'سازمان‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت کلان';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')->label('نام')->required(),
                Select::make('owner_user_id')
                    ->label('مالک')
                    ->options(fn () => (config('auth.providers.users.model'))::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('shared_data_mode')
                    ->label('حالت داده مشترک')
                    ->options([
                        'isolated' => 'ایزوله',
                        'shared_by_organization' => 'اشتراکی در سازمان',
                    ])
                    ->required(),
                TextInput::make('settings.entitlements.plan')->label('پلن سازمان')->nullable(),
                Select::make('settings.entitlements.status')
                    ->label('وضعیت پلن')
                    ->options([
                        'active' => 'فعال',
                        'trial' => 'آزمایشی',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active'),
                DateTimePicker::make('settings.entitlements.starts_at')->label('شروع پلن')->nullable(),
                DateTimePicker::make('settings.entitlements.ends_at')->label('پایان پلن')->nullable(),
                DateTimePicker::make('settings.entitlements.trial_ends_at')->label('پایان دوره آزمایشی')->nullable(),
                TextInput::make('settings.entitlements.max_tenants')
                    ->label('حداکثر فضای کاری')
                    ->numeric()
                    ->nullable(),
                TextInput::make('settings.entitlements.max_users')
                    ->label('حداکثر کاربران')
                    ->numeric()
                    ->nullable(),
                CheckboxList::make('settings.entitlements.modules')
                    ->label('ماژول‌های فعال')
                    ->options(fn () => app(ModuleCatalog::class)->moduleOptions())
                    ->columns(2)
                    ->searchable(),
                Textarea::make('settings.entitlements.notes')->label('یادداشت پلن')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('shared_data_mode')->label('حالت اشتراک'),
                TextColumn::make('created_at')->label('ایجاد'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function canCreate(): bool
    {
        return MegaSuperAdmin::check(auth()->user()) && parent::canCreate();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }
}
