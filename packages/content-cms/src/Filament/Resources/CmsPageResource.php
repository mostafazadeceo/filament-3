<?php

namespace Haida\ContentCms\Filament\Resources;

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
use Haida\ContentCms\Filament\Resources\CmsPageResource\Pages\CreateCmsPage;
use Haida\ContentCms\Filament\Resources\CmsPageResource\Pages\EditCmsPage;
use Haida\ContentCms\Filament\Resources\CmsPageResource\Pages\ListCmsPages;
use Haida\ContentCms\Models\CmsPage;
use Haida\ContentCms\Services\CmsPageService;
use Haida\SiteBuilderCore\Models\Site;

class CmsPageResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'cms.page';

    protected static ?string $model = CmsPage::class;

    protected static ?string $modelLabel = 'صفحه';

    protected static ?string $pluralModelLabel = 'صفحات';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'مدیریت محتوا';

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
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('اسلاگ')
                    ->required()
                    ->maxLength(255),
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
                    ->label('محتوا (JSON)')
                    ->rows(10)
                    ->required()
                    ->rules(['required', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return [];
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : [];
                    }),
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
                    ->visible(fn (CmsPage $record) => $record->status !== 'published')
                    ->authorize(fn (CmsPage $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (CmsPage $record) => app(CmsPageService::class)
                        ->publish($record, auth()->id())),
                Action::make('rollback')
                    ->label('بازگشت')
                    ->visible(fn (CmsPage $record) => (bool) $record->published_content)
                    ->authorize(fn (CmsPage $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (CmsPage $record) => app(CmsPageService::class)
                        ->rollbackToPublished($record, auth()->id())),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCmsPages::route('/'),
            'create' => CreateCmsPage::route('/create'),
            'edit' => EditCmsPage::route('/{record}/edit'),
        ];
    }
}
