<?php

namespace Haida\PlatformCore\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\PlatformCore\Filament\Resources\PluginRegistryResource\Pages\EditPluginRegistry;
use Haida\PlatformCore\Filament\Resources\PluginRegistryResource\Pages\ListPluginRegistries;
use Haida\PlatformCore\Models\PluginRegistry;

class PluginRegistryResource extends IamResource
{
    protected static ?string $permissionPrefix = 'platform.plugins';

    protected static ?string $model = PluginRegistry::class;

    protected static ?string $modelLabel = 'افزونه';

    protected static ?string $pluralModelLabel = 'افزونه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string|\UnitEnum|null $navigationGroup = 'زیرساخت';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('plugin_key')
                    ->label('کلید افزونه')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('name_fa')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description_fa')
                    ->label('توضیح')
                    ->rows(3)
                    ->nullable(),
                TextInput::make('version')
                    ->label('نسخه')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('created_at_jalali')
                    ->label('تاریخ جداسازی')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        PluginRegistry::STATUS_INSTALLED => 'نصب شده',
                        PluginRegistry::STATUS_DISABLED => 'غیرفعال',
                    ])
                    ->required(),
                TextInput::make('installed_at')
                    ->label('زمان نصب')
                    ->disabled()
                    ->dehydrated(false),
                KeyValue::make('metadata')
                    ->label('متادیتا')
                    ->columnSpanFull()
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('plugin_key')
                    ->label('کلید')
                    ->searchable(),
                TextColumn::make('name_fa')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('version')
                    ->label('نسخه'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('installed_at')
                    ->label('نصب')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPluginRegistries::route('/'),
            'edit' => EditPluginRegistry::route('/{record}/edit'),
        ];
    }
}
