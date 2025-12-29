<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\UomResource\Pages\CreateUom;
use Vendor\FilamentAccountingIr\Filament\Resources\UomResource\Pages\EditUom;
use Vendor\FilamentAccountingIr\Filament\Resources\UomResource\Pages\ListUoms;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\Uom;

class UomResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = Uom::class;

    protected static ?string $modelLabel = 'واحد اندازه‌گیری';

    protected static ?string $pluralModelLabel = 'واحدهای اندازه‌گیری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $navigationLabel = 'واحدهای اندازه‌گیری';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاعات پایه';

    protected static ?int $navigationSort = 4;

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
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(32),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                Toggle::make('is_default')
                    ->label('پیش‌فرض')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable(),
                TextColumn::make('name')->label('نام')->searchable(),
                ToggleColumn::make('is_default')->label('پیش‌فرض'),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUoms::route('/'),
            'create' => CreateUom::route('/create'),
            'edit' => EditUom::route('/{record}/edit'),
        ];
    }
}
