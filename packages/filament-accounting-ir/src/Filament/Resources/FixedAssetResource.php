<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\FixedAssetResource\Pages\CreateFixedAsset;
use Vendor\FilamentAccountingIr\Filament\Resources\FixedAssetResource\Pages\EditFixedAsset;
use Vendor\FilamentAccountingIr\Filament\Resources\FixedAssetResource\Pages\ListFixedAssets;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\FixedAsset;

class FixedAssetResource extends Resource
{
    use InteractsWithTenant;

    protected static ?string $model = FixedAsset::class;

    protected static ?string $modelLabel = 'دارایی ثابت';

    protected static ?string $pluralModelLabel = 'دارایی‌های ثابت';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'دارایی‌های ثابت';

    protected static string|\UnitEnum|null $navigationGroup = 'دارایی ثابت';

    protected static ?int $navigationSort = 1;

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
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('asset_code')
                    ->label('کد دارایی')
                    ->maxLength(64),
                TextInput::make('category')
                    ->label('گروه')
                    ->maxLength(255),
                DatePicker::make('acquisition_date')
                    ->label('تاریخ خرید'),
                TextInput::make('cost')
                    ->label('بهای تمام‌شده')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('salvage_value')
                    ->label('ارزش اسقاط')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('useful_life_months')
                    ->label('عمر (ماه)')
                    ->numeric()
                    ->minValue(1)
                    ->default(12),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'disposed' => 'واگذار شده',
                    ])
                    ->default('active'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('asset_code')->label('کد')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('cost')->label('بها')->numeric(decimalPlaces: 0),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFixedAssets::route('/'),
            'create' => CreateFixedAsset::route('/create'),
            'edit' => EditFixedAsset::route('/{record}/edit'),
        ];
    }
}
