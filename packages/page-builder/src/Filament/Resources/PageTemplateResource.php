<?php

namespace Haida\PageBuilder\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\PageBuilder\Filament\Resources\PageTemplateResource\Pages\CreatePageTemplate;
use Haida\PageBuilder\Filament\Resources\PageTemplateResource\Pages\EditPageTemplate;
use Haida\PageBuilder\Filament\Resources\PageTemplateResource\Pages\ListPageTemplates;
use Haida\PageBuilder\Models\PageTemplate;
use Haida\PageBuilder\Services\PageBuilderService;
use Haida\SiteBuilderCore\Models\Site;

class PageTemplateResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'page_builder.template';

    protected static ?string $model = PageTemplate::class;

    protected static ?string $modelLabel = 'قالب صفحه';

    protected static ?string $pluralModelLabel = 'قالب های صفحه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static string|\UnitEnum|null $navigationGroup = 'صفحه ساز';

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
                TextInput::make('key')
                    ->label('کلید')
                    ->helperText('فقط حروف انگلیسی، عدد و خط تیره')
                    ->regex('/^[a-z0-9-]+$/')
                    ->required()
                    ->maxLength(255),
                Textarea::make('schema')
                    ->label('اسکیمای بخش ها (JSON)')
                    ->rows(8)
                    ->rules(['nullable', 'json'])
                    ->afterStateHydrated(fn (Textarea $component, $state) => $component->state(static::encodeJson($state)))
                    ->dehydrateStateUsing(fn ($state) => static::decodeJson($state)),
                Textarea::make('draft_content')
                    ->label('پیش نویس (JSON)')
                    ->rows(12)
                    ->rules(['nullable', 'json'])
                    ->afterStateHydrated(fn (Textarea $component, $state) => $component->state(static::encodeJson($state)))
                    ->dehydrateStateUsing(fn ($state) => static::decodeJson($state)),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('site.name')->label('سایت'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('updated_at')->label('به روزرسانی')->jalaliDate(),
            ])
            ->actions([
                Action::make('publish')
                    ->label('انتشار')
                    ->visible(fn (PageTemplate $record) => $record->status !== 'published')
                    ->authorize(fn (PageTemplate $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PageTemplate $record) => app(PageBuilderService::class)
                        ->publish($record, auth()->id())),
                Action::make('rollback')
                    ->label('بازگشت')
                    ->visible(fn (PageTemplate $record) => $record->published_content !== null)
                    ->authorize(fn (PageTemplate $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (PageTemplate $record) => app(PageBuilderService::class)
                        ->rollbackToPublished($record, auth()->id())),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageTemplates::route('/'),
            'create' => CreatePageTemplate::route('/create'),
            'edit' => EditPageTemplate::route('/{record}/edit'),
        ];
    }

    private static function encodeJson($state): string
    {
        if (is_string($state)) {
            return $state;
        }

        if (! is_array($state)) {
            return '';
        }

        return json_encode($state, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?: '';
    }

    private static function decodeJson($state): ?array
    {
        if (! is_string($state) || trim($state) === '') {
            return null;
        }

        $decoded = json_decode($state, true);

        return is_array($decoded) ? $decoded : null;
    }
}
