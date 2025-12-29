<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource\Pages\CreateIntegrationConnector;
use Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource\Pages\EditIntegrationConnector;
use Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource\Pages\ListIntegrationConnectors;
use Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource\RelationManagers\IntegrationMappingsRelationManager;
use Vendor\FilamentAccountingIr\Filament\Resources\IntegrationConnectorResource\RelationManagers\IntegrationRunsRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\IntegrationConnector;

class IntegrationConnectorResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = IntegrationConnector::class;

    protected static ?string $modelLabel = 'کانکتور یکپارچه‌سازی';

    protected static ?string $pluralModelLabel = 'کانکتورهای یکپارچه‌سازی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?string $navigationLabel = 'یکپارچه‌سازی';

    protected static string|\UnitEnum|null $navigationGroup = 'یکپارچه‌سازی';

    protected static ?int $navigationSort = 1;

    protected static array $eagerLoad = ['company'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Select::make('connector_type')
                    ->label('نوع')
                    ->options([
                        'rest' => 'REST',
                        'csv' => 'CSV',
                        'sftp' => 'SFTP',
                    ])
                    ->default('rest'),
                TextInput::make('schedule')
                    ->label('زمان‌بندی')
                    ->helperText('مثال: every15Minutes یا cron')
                    ->maxLength(64),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                KeyValue::make('config')
                    ->label('پیکربندی')
                    ->keyLabel('کلید')
                    ->valueLabel('مقدار'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('connector_type')->label('نوع')->badge(),
                ToggleColumn::make('is_active')->label('فعال'),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            IntegrationMappingsRelationManager::class,
            IntegrationRunsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIntegrationConnectors::route('/'),
            'create' => CreateIntegrationConnector::route('/create'),
            'edit' => EditIntegrationConnector::route('/{record}/edit'),
        ];
    }
}
