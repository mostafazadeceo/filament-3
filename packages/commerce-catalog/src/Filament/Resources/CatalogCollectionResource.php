<?php

namespace Haida\CommerceCatalog\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource\Pages\CreateCatalogCollection;
use Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource\Pages\EditCatalogCollection;
use Haida\CommerceCatalog\Filament\Resources\CatalogCollectionResource\Pages\ListCatalogCollections;
use Haida\CommerceCatalog\Models\CatalogCollection;
use Haida\SiteBuilderCore\Models\Site;

class CatalogCollectionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'catalog.collection';

    protected static ?string $model = CatalogCollection::class;

    protected static ?string $modelLabel = 'مجموعه';

    protected static ?string $pluralModelLabel = 'مجموعه ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'کاتالوگ فروش';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('اسلاگ')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش نویس',
                        'published' => 'منتشر شده',
                    ])
                    ->default('draft')
                    ->required(),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->nullable(),
                Select::make('products')
                    ->label('محصولات')
                    ->relationship('products', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('slug')->label('اسلاگ')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('site.name')->label('سایت'),
                TextColumn::make('published_at')->label('انتشار')->jalaliDate(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش نویس',
                        'published' => 'منتشر شده',
                    ]),
                SelectFilter::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray()),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCatalogCollections::route('/'),
            'create' => CreateCatalogCollection::route('/create'),
            'edit' => EditCatalogCollection::route('/{record}/edit'),
        ];
    }
}
