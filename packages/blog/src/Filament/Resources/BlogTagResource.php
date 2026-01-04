<?php

namespace Haida\Blog\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\Blog\Filament\Resources\BlogTagResource\Pages\CreateBlogTag;
use Haida\Blog\Filament\Resources\BlogTagResource\Pages\EditBlogTag;
use Haida\Blog\Filament\Resources\BlogTagResource\Pages\ListBlogTags;
use Haida\Blog\Models\BlogTag;
use Haida\SiteBuilderCore\Models\Site;

class BlogTagResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'blog.tag';

    protected static ?string $model = BlogTag::class;

    protected static ?string $modelLabel = 'برچسب';

    protected static ?string $pluralModelLabel = 'برچسب ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

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
            'index' => ListBlogTags::route('/'),
            'create' => CreateBlogTag::route('/create'),
            'edit' => EditBlogTag::route('/{record}/edit'),
        ];
    }
}
