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
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\KeyMaterialResource\Pages\CreateKeyMaterial;
use Vendor\FilamentAccountingIr\Filament\Resources\KeyMaterialResource\Pages\EditKeyMaterial;
use Vendor\FilamentAccountingIr\Filament\Resources\KeyMaterialResource\Pages\ListKeyMaterials;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\KeyMaterial;

class KeyMaterialResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = KeyMaterial::class;

    protected static ?string $modelLabel = 'کلید سامانه مؤدیان';

    protected static ?string $pluralModelLabel = 'کلیدهای سامانه مؤدیان';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'کلیدها';

    protected static string|\UnitEnum|null $navigationGroup = 'سامانه مؤدیان';

    protected static ?int $navigationSort = 3;

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
                Select::make('material_type')
                    ->label('نوع کلید')
                    ->options([
                        'taxpayer_id' => 'شناسه مودی',
                        'private_key' => 'کلید خصوصی',
                        'certificate' => 'گواهی امضا',
                        'api_token' => 'توکن API',
                    ])
                    ->required(),
                TextInput::make('encrypted_value')
                    ->label('مقدار')
                    ->password()
                    ->revealable()
                    ->required(),
                DatePicker::make('effective_from')
                    ->label('از تاریخ'),
                DatePicker::make('effective_to')
                    ->label('تا تاریخ'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('material_type')->label('نوع')->badge(),
                TextColumn::make('effective_from')->label('از')->jalaliDate(),
                TextColumn::make('effective_to')->label('تا')->jalaliDate(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->defaultSort('effective_from', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKeyMaterials::route('/'),
            'create' => CreateKeyMaterial::route('/create'),
            'edit' => EditKeyMaterial::route('/{record}/edit'),
        ];
    }
}
