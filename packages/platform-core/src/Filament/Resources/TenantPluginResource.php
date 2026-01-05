<?php

namespace Haida\PlatformCore\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\PlatformCore\Filament\Resources\TenantPluginResource\Pages\CreateTenantPlugin;
use Haida\PlatformCore\Filament\Resources\TenantPluginResource\Pages\EditTenantPlugin;
use Haida\PlatformCore\Filament\Resources\TenantPluginResource\Pages\ListTenantPlugins;
use Haida\PlatformCore\Models\PluginRegistry;
use Haida\PlatformCore\Models\TenantPlugin;
use Illuminate\Support\Facades\Schema as SchemaFacade;

class TenantPluginResource extends IamResource
{
    protected static ?string $permissionPrefix = 'platform.plugins';

    protected static ?string $model = TenantPlugin::class;

    protected static ?string $modelLabel = 'افزونه تننت';

    protected static ?string $pluralModelLabel = 'افزونه‌های تننت';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'زیرساخت';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('tenant_id')
                    ->label('تننت')
                    ->relationship('tenant', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('plugin_key')
                    ->label('افزونه')
                    ->options(function () {
                        $table = (new PluginRegistry)->getTable();
                        if (! SchemaFacade::hasTable($table)) {
                            return [];
                        }

                        return PluginRegistry::query()->orderBy('name_fa')->pluck('name_fa', 'plugin_key')->toArray();
                    })
                    ->searchable()
                    ->required(),
                Toggle::make('enabled')
                    ->label('فعال')
                    ->default(true),
                DateTimePicker::make('starts_at')
                    ->label('شروع')
                    ->seconds(false)
                    ->nullable(),
                DateTimePicker::make('ends_at')
                    ->label('پایان')
                    ->seconds(false)
                    ->nullable(),
                KeyValue::make('limits')
                    ->label('محدودیت‌ها')
                    ->columnSpanFull()
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('تننت')
                    ->searchable(),
                TextColumn::make('plugin.name_fa')
                    ->label('افزونه')
                    ->searchable(),
                IconColumn::make('enabled')
                    ->label('فعال')
                    ->boolean(),
                TextColumn::make('starts_at')
                    ->label('شروع')
                    ->jalaliDateTime(),
                TextColumn::make('ends_at')
                    ->label('پایان')
                    ->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('plugin_key')
                    ->label('افزونه')
                    ->options(function () {
                        $table = (new PluginRegistry)->getTable();
                        if (! SchemaFacade::hasTable($table)) {
                            return [];
                        }

                        return PluginRegistry::query()->orderBy('name_fa')->pluck('name_fa', 'plugin_key')->toArray();
                    }),
                SelectFilter::make('enabled')
                    ->label('وضعیت')
                    ->options([
                        '1' => 'فعال',
                        '0' => 'غیرفعال',
                    ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantPlugins::route('/'),
            'create' => CreateTenantPlugin::route('/create'),
            'edit' => EditTenantPlugin::route('/{record}/edit'),
        ];
    }
}
