<?php

namespace Haida\Blog\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\Blog\Filament\Resources\BlogPostResource\Pages\CreateBlogPost;
use Haida\Blog\Filament\Resources\BlogPostResource\Pages\EditBlogPost;
use Haida\Blog\Filament\Resources\BlogPostResource\Pages\ListBlogPosts;
use Haida\Blog\Models\BlogCategory;
use Haida\Blog\Models\BlogPost;
use Haida\Blog\Services\BlogPostService;
use Haida\SiteBuilderCore\Models\Site;

class BlogPostResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'blog.post';

    protected static ?string $model = BlogPost::class;

    protected static ?string $modelLabel = 'نوشته';

    protected static ?string $pluralModelLabel = 'نوشته ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-newspaper';

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
                Select::make('category_id')
                    ->label('دسته بندی')
                    ->options(fn () => BlogCategory::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('اسلاگ')
                    ->required()
                    ->maxLength(255),
                Textarea::make('excerpt')
                    ->label('خلاصه')
                    ->rows(3)
                    ->nullable(),
                Textarea::make('seo')
                    ->label('سئو (JSON)')
                    ->helperText('کلیدهای متداول: title, description, image, og_type')
                    ->rows(4)
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
                Textarea::make('draft_content')
                    ->label('محتوا (HTML)')
                    ->rows(10)
                    ->required(),
                Select::make('tags')
                    ->label('برچسب ها')
                    ->relationship('tags', 'name')
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
                TextColumn::make('title')->label('عنوان')->searchable()->sortable(),
                TextColumn::make('slug')->label('اسلاگ')->searchable(),
                TextColumn::make('site.name')->label('سایت'),
                TextColumn::make('category.name')->label('دسته بندی'),
                TextColumn::make('status')->label('وضعیت')->badge(),
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
            ->actions([
                Action::make('publish')
                    ->label('انتشار')
                    ->visible(fn (BlogPost $record) => $record->status !== 'published')
                    ->authorize(fn (BlogPost $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (BlogPost $record) => app(BlogPostService::class)
                        ->publish($record, auth()->id())),
                Action::make('rollback')
                    ->label('بازگشت')
                    ->visible(fn (BlogPost $record) => (bool) $record->published_content)
                    ->authorize(fn (BlogPost $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (BlogPost $record) => app(BlogPostService::class)
                        ->rollbackToPublished($record, auth()->id())),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}
