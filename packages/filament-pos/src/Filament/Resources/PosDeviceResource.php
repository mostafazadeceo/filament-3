<?php

namespace Haida\FilamentPos\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPos\Filament\Resources\PosDeviceResource\Pages\CreatePosDevice;
use Haida\FilamentPos\Filament\Resources\PosDeviceResource\Pages\EditPosDevice;
use Haida\FilamentPos\Filament\Resources\PosDeviceResource\Pages\ListPosDevices;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosRegister;
use Illuminate\Database\Eloquent\Model;

class PosDeviceResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = PosDevice::class;

    protected static ?string $modelLabel = 'دستگاه پوز';

    protected static ?string $pluralModelLabel = 'دستگاه‌های پوز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-device-tablet';

    protected static string|\UnitEnum|null $navigationGroup = 'پوز';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('pos.manage_register');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('register_id')
                    ->label('صندوق')
                    ->options(fn () => PosRegister::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('device_uid')
                    ->label('شناسه دستگاه')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                DateTimePicker::make('last_seen_at')
                    ->label('آخرین مشاهده')
                    ->disabled()
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('device_uid')
                    ->label('شناسه دستگاه')
                    ->searchable(),
                TextColumn::make('register.name')
                    ->label('صندوق')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('last_seen_at')
                    ->label('آخرین مشاهده')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosDevices::route('/'),
            'create' => CreatePosDevice::route('/create'),
            'edit' => EditPosDevice::route('/{record}/edit'),
        ];
    }
}
