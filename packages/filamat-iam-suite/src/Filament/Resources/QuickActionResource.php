<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Filament\Resources\QuickActionResource\Pages\CreateQuickAction;
use Filamat\IamSuite\Filament\Resources\QuickActionResource\Pages\EditQuickAction;
use Filamat\IamSuite\Filament\Resources\QuickActionResource\Pages\ListQuickActions;
use Filamat\IamSuite\Models\QuickAction;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Navigation\NavigationItem;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

use function Filament\Support\generate_icon_html;

class QuickActionResource extends IamResource
{
    protected static ?string $model = QuickAction::class;

    protected static ?string $permissionPrefix = 'quick_actions';

    protected static ?string $navigationLabel = 'اقدامات سریع';

    protected static ?string $pluralModelLabel = 'اقدامات سریع';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rocket-launch';

    protected static string|\UnitEnum|null $navigationGroup = 'داشبورد';

    public static function form(Schema $schema): Schema
    {
        $navigationCatalog = static::navigationCatalog();
        $navigationOptions = static::navigationOptions($navigationCatalog);

        return $schema
            ->schema([
                Section::make('تعریف اقدام سریع')
                    ->schema([
                        Select::make('type')
                            ->label('نوع')
                            ->options([
                                'navigation' => 'منوی پنل',
                                'custom' => 'لینک سفارشی',
                            ])
                            ->default(fn () => $navigationOptions === [] ? 'custom' : 'navigation')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if ($state === 'custom') {
                                    $set('navigation_key', null);
                                }
                            }),
                        Select::make('navigation_key')
                            ->label('منوی قابل دسترس')
                            ->options($navigationOptions)
                            ->searchable()
                            ->live()
                            ->dehydrated(false)
                            ->visible(fn (Get $get): bool => $get('type') === 'navigation')
                            ->required(fn (Get $get): bool => $get('type') === 'navigation')
                            ->afterStateUpdated(function (?string $state, Set $set) use ($navigationCatalog): void {
                                $item = $state ? ($navigationCatalog[$state] ?? null) : null;
                                if (! $item) {
                                    return;
                                }

                                $set('label', $item['label']);
                                $set('url', $item['url']);
                                $set('icon', $item['icon'] ?: 'heroicon-o-link');
                                $set('description', $item['description']);
                            }),
                        TextInput::make('label')
                            ->label('عنوان')
                            ->maxLength(255)
                            ->required(),
                        Textarea::make('description')
                            ->label('توضیح')
                            ->rows(2)
                            ->maxLength(255)
                            ->nullable()
                            ->columnSpanFull(),
                        TextInput::make('url')
                            ->label('لینک')
                            ->maxLength(2048)
                            ->required()
                            ->readOnly(fn (Get $get): bool => $get('type') === 'navigation'),
                        Select::make('icon')
                            ->label('آیکون')
                            ->options(static::iconOptions())
                            ->searchable()
                            ->allowHtml()
                            ->native(false)
                            ->required()
                            ->helperText('از آیکون‌های آماده استفاده کنید.'),
                    ])
                    ->columns(2),
                Section::make('نمایش')
                    ->schema([
                        TextInput::make('rank')
                            ->label('رتبه')
                            ->numeric()
                            ->default(fn () => static::nextRank())
                            ->required(),
                        Toggle::make('is_active')
                            ->label('فعال')
                            ->default(true),
                        Hidden::make('tenant_id')
                            ->default(fn () => TenantContext::getTenantId()),
                        Hidden::make('sort')
                            ->default(fn () => static::nextSort()),
                        Hidden::make('user_id')
                            ->default(fn () => auth()->id())
                            ->required(),
                        Hidden::make('panel_id')
                            ->default(fn () => Filament::getCurrentPanel()?->getId())
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('عنوان')->searchable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'navigation' => 'منوی پنل',
                        'custom' => 'لینک سفارشی',
                        default => $state,
                    }),
                TextColumn::make('rank')->label('رتبه')->sortable(),
                TextColumn::make('icon')->label('آیکون')->limit(24),
                TextColumn::make('url')->label('لینک')->limit(40),
                IconColumn::make('is_active')->label('فعال')->boolean(),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuickActions::route('/'),
            'create' => CreateQuickAction::route('/create'),
            'edit' => EditQuickAction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $userId = auth()->id();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        if ($panelId) {
            $query->where('panel_id', $panelId);
        }

        $tenantId = TenantContext::getTenantId();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } else {
            $query->whereNull('tenant_id');
        }

        return $query;
    }

    /**
     * @return array<string, array{label: string, full_label: string, description: string|null, url: string, icon: string|null}>
     */
    protected static function navigationCatalog(): array
    {
        $catalog = [];
        $items = Filament::getNavigationItems();

        static::collectNavigationItems($items, $catalog);

        return $catalog;
    }

    /**
     * @param  array<NavigationItem> | Arrayable  $items
     * @param  array<string, array{label: string, description: string|null, url: string, icon: string|null}>  $catalog
     */
    protected static function collectNavigationItems(array|Arrayable $items, array &$catalog, string $prefix = ''): void
    {
        foreach ($items as $item) {
            if (! $item instanceof NavigationItem) {
                continue;
            }

            $label = $item->getLabel();
            $url = $item->getUrl();
            $group = $item->getGroup();
            $groupLabel = $group instanceof \BackedEnum
                ? $group->value
                : ($group instanceof \UnitEnum ? $group->name : (is_string($group) ? $group : null));
            $icon = $item->getIcon();
            $iconValue = $icon instanceof \BackedEnum
                ? $icon->value
                : (is_string($icon) ? $icon : null);

            $fullLabel = trim(($groupLabel ? $groupLabel.' / ' : '').($prefix ? $prefix.' / ' : '').$label);

            if ($url) {
                $key = sha1($fullLabel.'|'.$url);
                $catalog[$key] = [
                    'label' => $label,
                    'full_label' => $fullLabel ?: $label,
                    'description' => $fullLabel ? ('دسترسی سریع به '.$fullLabel) : null,
                    'url' => $url,
                    'icon' => $iconValue,
                ];
            }

            $children = $item->getChildItems();
            if ($children) {
                static::collectNavigationItems($children, $catalog, $fullLabel ?: $label);
            }
        }
    }

    /**
     * @param  array<string, array{label: string, full_label?: string, description: string|null, url: string, icon: string|null}>  $catalog
     * @return array<string, string>
     */
    protected static function navigationOptions(array $catalog): array
    {
        $options = [];
        foreach ($catalog as $key => $item) {
            $label = $item['full_label'] ?? ($item['label'] ?? '');
            if ($label === '') {
                continue;
            }

            $options[$key] = $label;
        }

        return $options;
    }

    protected static function nextRank(): int
    {
        $query = QuickAction::query();
        $userId = auth()->id();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        if ($panelId) {
            $query->where('panel_id', $panelId);
        }

        $tenantId = TenantContext::getTenantId();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } else {
            $query->whereNull('tenant_id');
        }

        $max = (int) $query->max('rank');

        return $max > 0 ? $max + 1 : 1;
    }

    protected static function nextSort(): int
    {
        $query = QuickAction::query();
        $userId = auth()->id();
        if ($userId) {
            $query->where('user_id', $userId);
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        if ($panelId) {
            $query->where('panel_id', $panelId);
        }

        $tenantId = TenantContext::getTenantId();
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        } else {
            $query->whereNull('tenant_id');
        }

        $max = (int) $query->max('sort');

        return $max > 0 ? $max + 1 : 1;
    }

    /**
     * @return array<string, string>
     */
    protected static function iconOptions(): array
    {
        $options = [];

        foreach (Heroicon::cases() as $icon) {
            $value = $icon->value;
            $label = Str::of($value)
                ->replace(['heroicon-o-', 'heroicon-s-', 'heroicon-m-'], '')
                ->replace('-', ' ')
                ->value();

            $iconHtml = generate_icon_html(
                $icon,
                attributes: new ComponentAttributeBag(['class' => 'h-4 w-4'])
            );

            $options[$value] = sprintf(
                '<span class="flex items-center gap-2">%s<span class="text-sm">%s</span></span>',
                $iconHtml?->toHtml() ?? '',
                e($label)
            );
        }

        return $options;
    }
}
