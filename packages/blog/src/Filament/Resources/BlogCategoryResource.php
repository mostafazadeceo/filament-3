<?php

namespace Haida\Blog\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\Blog\Filament\Resources\BlogCategoryResource\Pages\CreateBlogCategory;
use Haida\Blog\Filament\Resources\BlogCategoryResource\Pages\EditBlogCategory;
use Haida\Blog\Filament\Resources\BlogCategoryResource\Pages\ListBlogCategories;
use Haida\Blog\Models\BlogCategory;
use Haida\SiteBuilderCore\Models\Site;

class BlogCategoryResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'blog.category';

    protected static ?string $model = BlogCategory::class;

    protected static ?string $modelLabel = 'دسته بندی';

    protected static ?string $pluralModelLabel = 'دسته بندی ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'وبلاگ';

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
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('slug')->label('اسلاگ')->searchable(),
                TextColumn::make('site.name')->label('سایت'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogCategories::route('/'),
            'create' => CreateBlogCategory::route('/create'),
            'edit' => EditBlogCategory::route('/{record}/edit'),
        ];
    }
}
