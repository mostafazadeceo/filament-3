<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Filament\Resources\CommercePriceListResource\Pages\CreateCommercePriceList;
use Haida\FilamentCommerceCore\Filament\Resources\CommercePriceListResource\Pages\EditCommercePriceList;
use Haida\FilamentCommerceCore\Filament\Resources\CommercePriceListResource\Pages\ListCommercePriceLists;
use Haida\FilamentCommerceCore\Filament\Resources\CommercePriceListResource\RelationManagers\CommercePricesRelationManager;
use Haida\FilamentCommerceCore\Models\CommercePriceList;

class CommercePriceListResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.pricing';

    protected static ?string $model = CommercePriceList::class;

    protected static ?string $modelLabel = 'لیست قیمت';

    protected static ?string $pluralModelLabel = 'لیست قیمت ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(120),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('filament-commerce-core.defaults.currency', 'IRR'))
                    ->maxLength(8),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'archived' => 'بایگانی',
                    ])
                    ->default('active')
                    ->required(),
                DateTimePicker::make('starts_at')
                    ->label('شروع'),
                DateTimePicker::make('ends_at')
                    ->label('پایان'),
                Textarea::make('metadata')
                    ->label('متادیتا (JSON)')
                    ->rows(3)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('code')->label('کد')->searchable(),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('starts_at')->label('شروع')->jalaliDateTime(),
                TextColumn::make('ends_at')->label('پایان')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'archived' => 'بایگانی',
                    ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CommercePricesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommercePriceLists::route('/'),
            'create' => CreateCommercePriceList::route('/create'),
            'edit' => EditCommercePriceList::route('/{record}/edit'),
        ];
    }
}
