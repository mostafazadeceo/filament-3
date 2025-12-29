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
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceProviderResource\Pages\CreateEInvoiceProvider;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceProviderResource\Pages\EditEInvoiceProvider;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceProviderResource\Pages\ListEInvoiceProviders;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;

class EInvoiceProviderResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = EInvoiceProvider::class;

    protected static ?string $modelLabel = 'ارائه‌دهنده مؤدیان';

    protected static ?string $pluralModelLabel = 'ارائه‌دهندگان مؤدیان';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud';

    protected static ?string $navigationLabel = 'ارائه‌دهندگان مؤدیان';

    protected static string|\UnitEnum|null $navigationGroup = 'سامانه مؤدیان';

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
                TextInput::make('driver')
                    ->label('درایور')
                    ->default('mock')
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
                TextColumn::make('driver')->label('درایور')->badge(),
                ToggleColumn::make('is_active')->label('فعال'),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEInvoiceProviders::route('/'),
            'create' => CreateEInvoiceProvider::route('/create'),
            'edit' => EditEInvoiceProvider::route('/{record}/edit'),
        ];
    }
}
