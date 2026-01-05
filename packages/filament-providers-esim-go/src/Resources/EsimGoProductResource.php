<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoProductResource\Pages\ListEsimGoProducts;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoProductResource\Pages\ViewEsimGoProduct;
use Haida\ProvidersEsimGoCore\Models\EsimGoProduct;
use Haida\ProvidersEsimGoCore\Support\EsimGoLabels;
use Illuminate\Support\Str;

class EsimGoProductResource extends IamResource
{
    protected static ?string $model = EsimGoProduct::class;

    protected static ?string $permissionPrefix = 'esim_go.product';

    protected static ?string $modelLabel = 'محصول eSIM Go';

    protected static ?string $pluralModelLabel = 'محصولات eSIM Go';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'محصولات';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('bundle_name')
                    ->label('نام باندل')
                    ->searchable()
                    ->copyable(),
                IconColumn::make('catalog_product_id')
                    ->label('انتشار')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->sortable(),
                TextColumn::make('groups')
                    ->label('گروه‌ها')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->formatStateUsing(fn ($state) => static::formatGroups((array) $state)),
                TextColumn::make('region')
                    ->label('قاره/منطقه')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->formatStateUsing(fn ($state) => static::formatRegions((array) $state)),
                TextColumn::make('countries_meta')
                    ->label('کشورها')
                    ->listWithLineBreaks()
                    ->limitList(6)
                    ->expandableLimitedList()
                    ->state(fn (EsimGoProduct $record) => static::formatCountries($record)),
                TextColumn::make('countries_count')
                    ->label('تعداد کشور')
                    ->state(fn (EsimGoProduct $record) => static::countCountries($record))
                    ->badge()
                    ->toggleable(),
                TextColumn::make('duration_days')->label('مدت')->suffix(' روز')->toggleable(),
                TextColumn::make('data_amount_mb')
                    ->label('حجم')
                    ->formatStateUsing(fn ($state) => static::formatDataAmount(is_numeric($state) ? (int) $state : null))
                    ->toggleable(),
                TextColumn::make('speed')
                    ->label('سرعت')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->formatStateUsing(fn ($state) => array_values(array_filter((array) $state))),
                TextColumn::make('features')
                    ->label('مزایا')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->state(fn (EsimGoProduct $record) => static::formatFeatures($record)),
                TextColumn::make('allowances')
                    ->label('سهمیه‌ها')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->expandableLimitedList()
                    ->state(fn (EsimGoProduct $record) => static::formatAllowances($record)),
                TextColumn::make('price')->label('قیمت')->toggleable(),
                TextColumn::make('currency')->label('ارز')->toggleable(),
                TextColumn::make('billing_type')->label('نوع صورتحساب')->toggleable(),
                TextColumn::make('status')->label('وضعیت'),
            ])
            ->groups([
                Group::make('primary_region')
                    ->label('قاره/منطقه')
                    ->getKeyFromRecordUsing(fn (EsimGoProduct $record) => static::primaryRegion($record) ?? 'نامشخص')
                    ->getTitleFromRecordUsing(fn (EsimGoProduct $record) => static::formatRegions([static::primaryRegion($record) ?? 'نامشخص'])[0] ?? 'نامشخص')
                    ->collapsible(),
                Group::make('primary_group')
                    ->label('گروه')
                    ->getKeyFromRecordUsing(fn (EsimGoProduct $record) => static::primaryGroup($record) ?? 'نامشخص')
                    ->getTitleFromRecordUsing(fn (EsimGoProduct $record) => static::formatGroups([static::primaryGroup($record) ?? 'نامشخص'])[0] ?? 'نامشخص')
                    ->collapsible(),
                Group::make('status')
                    ->label('وضعیت')
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->label('گروه')
                    ->searchable()
                    ->options(fn () => static::groupOptions())
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) {
                            return;
                        }
                        $query->whereJsonContains('groups', $value);
                    }),
                SelectFilter::make('region')
                    ->label('قاره/منطقه')
                    ->options(fn () => static::regionOptions())
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) {
                            return;
                        }
                        $query->whereJsonContains('region', $value);
                    }),
                SelectFilter::make('country')
                    ->label('کشور')
                    ->searchable()
                    ->options(fn () => static::countryOptions())
                    ->query(function ($query, array $data) {
                        $value = $data['value'] ?? null;
                        if (! $value) {
                            return;
                        }
                        $query->whereJsonContains('countries', $value);
                    }),
                SelectFilter::make('billing_type')
                    ->label('نوع صورتحساب')
                    ->options(fn () => EsimGoProduct::query()->whereNotNull('billing_type')->distinct()->pluck('billing_type', 'billing_type')->toArray()),
                SelectFilter::make('duration_days')
                    ->label('مدت (روز)')
                    ->options(fn () => EsimGoProduct::query()->whereNotNull('duration_days')->distinct()->orderBy('duration_days')->pluck('duration_days', 'duration_days')->toArray()),
                TernaryFilter::make('unlimited')
                    ->label('نامحدود')
                    ->trueLabel('نامحدود')
                    ->falseLabel('دارای محدودیت')
                    ->queries(
                        true: fn ($query) => $query->where('unlimited', true),
                        false: fn ($query) => $query->where('unlimited', false),
                    ),
                TernaryFilter::make('autostart')
                    ->label('شروع خودکار')
                    ->trueLabel('فعال')
                    ->falseLabel('غیرفعال')
                    ->queries(
                        true: fn ($query) => $query->where('autostart', true),
                        false: fn ($query) => $query->where('autostart', false),
                    ),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
            ])
            ->emptyStateHeading('محصولی ثبت نشده است')
            ->emptyStateDescription('ابتدا کاتالوگ را همگام‌سازی کنید.')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('همگام‌سازی کاتالوگ')
                    ->url(fn (): string => route('filament.admin.resources.esim-go-products.index'))
                    ->visible(false),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEsimGoProducts::route('/'),
            'view' => ViewEsimGoProduct::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('مشخصات محصول')
                ->schema([
                    TextEntry::make('bundle_name')->label('نام باندل'),
                    TextEntry::make('description')->label('توضیحات')->columnSpanFull(),
                    TextEntry::make('groups')
                        ->label('گروه‌ها')
                        ->formatStateUsing(fn ($state) => implode('، ', static::formatGroups((array) $state))),
                    TextEntry::make('region')
                        ->label('منطقه')
                        ->formatStateUsing(fn ($state) => implode('، ', static::formatRegions((array) $state))),
                    TextEntry::make('duration_days')->label('مدت')->suffix(' روز'),
                    TextEntry::make('data_amount_mb')->label('حجم')->suffix(' MB'),
                    TextEntry::make('price')
                        ->label('قیمت')
                        ->formatStateUsing(fn ($state, EsimGoProduct $record) => $record->price.' '.$record->currency),
                    TextEntry::make('autostart')
                        ->label('شروع خودکار')
                        ->badge()
                        ->formatStateUsing(fn ($state) => EsimGoLabels::boolean((bool) $state)),
                    TextEntry::make('unlimited')
                        ->label('نامحدود')
                        ->badge()
                        ->formatStateUsing(fn ($state) => EsimGoLabels::boolean((bool) $state)),
                ])
                ->columns(3),
            Section::make('پوشش جغرافیایی')
                ->schema([
                    RepeatableEntry::make('countries_meta')
                        ->label('کشورها')
                        ->schema([
                            TextEntry::make('name')
                                ->label('کشور')
                                ->formatStateUsing(function ($state, array $record) {
                                    $iso = $record['iso'] ?? null;
                                    $flag = static::flagEmoji(is_string($iso) ? $iso : null);

                                    return trim($flag.' '.(string) $state);
                                }),
                            TextEntry::make('iso')->label('کد'),
                            TextEntry::make('region')->label('قاره/منطقه'),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                    TextEntry::make('countries')
                        ->label('کشورها (خام)')
                        ->formatStateUsing(fn ($state) => implode('، ', array_values(array_filter((array) $state))))
                        ->columnSpanFull(),
                ])
                ->columns(1),
            Section::make('مزایا و محدودیت‌ها')
                ->schema([
                    TextEntry::make('features')
                        ->label('مزایا')
                        ->formatStateUsing(fn ($state, EsimGoProduct $record) => implode('، ', static::formatFeatures($record)))
                        ->columnSpanFull(),
                    TextEntry::make('speed')
                        ->label('سرعت')
                        ->formatStateUsing(fn ($state) => implode('، ', array_values(array_filter((array) $state)))),
                    TextEntry::make('roaming_enabled')
                        ->label('رومینگ فعال')
                        ->formatStateUsing(fn ($state) => implode('، ', array_values(array_filter((array) $state)))),
                    TextEntry::make('billing_type')->label('نوع صورتحساب'),
                    RepeatableEntry::make('allowances')
                        ->label('سهمیه‌ها')
                        ->schema([
                            TextEntry::make('type')->label('نوع'),
                            TextEntry::make('service')->label('سرویس'),
                            TextEntry::make('description')->label('توضیحات'),
                            TextEntry::make('amount')->label('مقدار'),
                            TextEntry::make('unit')->label('واحد'),
                            TextEntry::make('unlimited')
                                ->label('نامحدود')
                                ->formatStateUsing(fn ($state) => EsimGoLabels::boolean((bool) $state)),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected static function formatGroups(array $groups): array
    {
        $map = (array) config('filament-providers-esim-go.groups', []);

        return array_values(array_filter(array_map(function (string $group) use ($map) {
            $meta = $map[$group] ?? null;
            $label = is_array($meta) && ! empty($meta['label']) ? $meta['label'] : $group;
            $icon = is_array($meta) && ! empty($meta['icon']) ? $meta['icon'] : '🧩';
            $id = Str::slug($group, '_');

            return trim($icon.' '.$label.' #'.$id);
        }, $groups)));
    }

    /**
     * @return array<int, string>
     */
    protected static function formatRegions(array $regions): array
    {
        $map = (array) config('filament-providers-esim-go.regions', []);

        return array_values(array_filter(array_map(function (string $region) use ($map) {
            $label = $map[$region] ?? $region;
            $icon = static::regionIcon($region);

            return trim($icon.' '.$label);
        }, $regions)));
    }

    /**
     * @return array<int, string>
     */
    protected static function formatCountries(EsimGoProduct $record): array
    {
        $meta = (array) ($record->countries_meta ?? []);
        if ($meta === []) {
            return array_values(array_filter((array) $record->countries));
        }

        return array_values(array_filter(array_map(function (array $country): ?string {
            $name = $country['name'] ?? $country['iso'] ?? null;
            $iso = $country['iso'] ?? null;
            $flag = static::flagEmoji($iso);
            if (! $name) {
                return null;
            }

            return trim($flag.' '.$name);
        }, $meta)));
    }

    /**
     * @return array<int, string>
     */
    protected static function formatFeatures(EsimGoProduct $record): array
    {
        $features = [];

        if ((bool) $record->unlimited) {
            $features[] = '♾️ نامحدود';
        }

        if ((bool) $record->autostart) {
            $features[] = '⚡ شروع خودکار';
        }

        $roaming = array_values(array_filter((array) $record->roaming_enabled));
        if ($roaming !== []) {
            $features[] = '📡 رومینگ: '.implode('، ', $roaming);
        }

        $allowances = array_values(array_filter((array) $record->allowances));
        foreach ($allowances as $allowance) {
            if (! is_array($allowance)) {
                continue;
            }
            $service = $allowance['service'] ?? $allowance['type'] ?? null;
            $amount = $allowance['amount'] ?? null;
            $unit = $allowance['unit'] ?? null;
            $desc = trim(implode(' ', array_filter([
                is_string($service) ? $service : null,
                is_numeric($amount) ? (string) $amount : null,
                is_string($unit) ? $unit : null,
            ])));
            if ($desc !== '') {
                $features[] = '📦 '.$desc;
            }
        }

        return array_values(array_unique($features));
    }

    /**
     * @return array<int, string>
     */
    protected static function formatAllowances(EsimGoProduct $record): array
    {
        $allowances = array_values(array_filter((array) $record->allowances));
        $items = [];

        foreach ($allowances as $allowance) {
            if (! is_array($allowance)) {
                continue;
            }

            $service = $allowance['service'] ?? $allowance['type'] ?? null;
            $amount = $allowance['amount'] ?? null;
            $unit = $allowance['unit'] ?? null;
            $unlimited = (bool) ($allowance['unlimited'] ?? false);

            $label = trim(implode(' ', array_filter([
                is_string($service) ? $service : null,
                $unlimited ? 'نامحدود' : (is_numeric($amount) ? (string) $amount : null),
                $unlimited ? null : (is_string($unit) ? $unit : null),
            ])));

            if ($label !== '') {
                $items[] = '📦 '.$label;
            }
        }

        return array_values(array_unique($items));
    }

    protected static function formatDataAmount(?int $mb): string
    {
        if (! $mb) {
            return '-';
        }

        if ($mb >= 1024) {
            $gb = round($mb / 1024, 1);

            return $gb.' GB';
        }

        return $mb.' MB';
    }

    protected static function regionIcon(string $region): string
    {
        return match (strtolower($region)) {
            'europe' => '🌍',
            'asia' => '🌏',
            'middle east' => '🌍',
            'north america' => '🌎',
            'south america' => '🌎',
            'africa' => '🌍',
            'oceania' => '🌏',
            default => '🗺️',
        };
    }

    protected static function flagEmoji(?string $iso): string
    {
        if (! $iso || strlen($iso) !== 2) {
            return '';
        }

        $iso = strtoupper($iso);
        $a = mb_ord($iso[0]) - 65 + 0x1F1E6;
        $b = mb_ord($iso[1]) - 65 + 0x1F1E6;

        return mb_chr($a).mb_chr($b);
    }

    protected static function primaryRegion(EsimGoProduct $record): ?string
    {
        $region = (array) $record->region;

        return $region[0] ?? null;
    }

    protected static function primaryGroup(EsimGoProduct $record): ?string
    {
        $groups = (array) $record->groups;

        return $groups[0] ?? null;
    }

    protected static function countCountries(EsimGoProduct $record): int
    {
        $meta = (array) ($record->countries_meta ?? []);
        if ($meta !== []) {
            return count($meta);
        }

        return count(array_values(array_filter((array) $record->countries)));
    }

    /**
     * @return array<string, string>
     */
    protected static function distinctJsonOptions(string $column): array
    {
        return EsimGoProduct::query()
            ->select($column)
            ->whereNotNull($column)
            ->get()
            ->flatMap(fn (EsimGoProduct $record) => (array) $record->{$column})
            ->filter()
            ->unique()
            ->sort()
            ->mapWithKeys(fn (string $value) => [$value => $value])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected static function groupOptions(): array
    {
        $map = (array) config('filament-providers-esim-go.groups', []);
        $values = static::distinctJsonOptions('groups');

        return collect($values)->mapWithKeys(function ($label, $key) use ($map) {
            $meta = $map[$key] ?? null;
            $display = is_array($meta) && ! empty($meta['label']) ? $meta['label'] : $label;
            $icon = is_array($meta) && ! empty($meta['icon']) ? $meta['icon'] : '🧩';
            $id = Str::slug((string) $key, '_');

            return [$key => trim($icon.' '.$display.' #'.$id)];
        })->all();
    }

    /**
     * @return array<string, string>
     */
    protected static function regionOptions(): array
    {
        $regions = static::distinctJsonOptions('region');
        $labels = (array) config('filament-providers-esim-go.regions', []);

        return collect($regions)->mapWithKeys(function ($label, $key) use ($labels) {
            $display = $labels[$key] ?? $label;
            $icon = static::regionIcon((string) $key);

            return [$key => trim($icon.' '.$display)];
        })->all();
    }

    /**
     * @return array<string, string>
     */
    protected static function countryOptions(): array
    {
        return EsimGoProduct::query()
            ->select(['countries', 'countries_meta'])
            ->get()
            ->flatMap(function (EsimGoProduct $record) {
                $meta = (array) ($record->countries_meta ?? []);
                if ($meta === []) {
                    return collect((array) $record->countries)->map(function (string $name) {
                        return ['name' => $name, 'iso' => null];
                    });
                }

                return collect($meta)->map(fn (array $item) => [
                    'name' => (string) ($item['name'] ?? $item['iso'] ?? ''),
                    'iso' => $item['iso'] ?? null,
                ]);
            })
            ->filter(fn (array $item) => $item['name'] !== '')
            ->unique('name')
            ->sortBy('name')
            ->mapWithKeys(function (array $item) {
                $flag = static::flagEmoji(is_string($item['iso']) ? $item['iso'] : null);
                $label = trim($flag.' '.$item['name']);

                return [$item['name'] => $label];
            })
            ->all();
    }

    public static function getNavigationGroup(): ?string
    {
        return config('filament-providers-esim-go.navigation.group', 'Providerها');
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-providers-esim-go.navigation.sort', 30);
    }
}
