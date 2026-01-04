<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCategoryResource\Pages\CreateCommerceCategory;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCategoryResource\Pages\EditCommerceCategory;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceCategoryResource\Pages\ListCommerceCategories;
use Haida\FilamentCommerceCore\Models\CommerceCategory;

class CommerceCategoryResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.catalog';

    protected static ?string $model = CommerceCategory::class;

    protected static ?string $modelLabel = 'دسته بندی';

    protected static ?string $pluralModelLabel = 'دسته بندی ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('parent_id')
                    ->label('دسته والد')
                    ->options(fn () => CommerceCategory::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('اسلاگ')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('slug')->label('اسلاگ'),
                TextColumn::make('parent.name')->label('دسته والد'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('sort_order')->label('ترتیب'),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceCategories::route('/'),
            'create' => CreateCommerceCategory::route('/create'),
            'edit' => EditCommerceCategory::route('/{record}/edit'),
        ];
    }
}
