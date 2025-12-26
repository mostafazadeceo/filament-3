<?php

namespace Haida\FilamentRelograde\Resources;

use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentRelograde\Jobs\SyncBrandsJob;
use Haida\FilamentRelograde\Models\RelogradeBrand;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeProduct;
use Haida\FilamentRelograde\Resources\RelogradeBrandResource\Pages\ListRelogradeBrands;
use Haida\FilamentRelograde\Resources\RelogradeBrandResource\Pages\ViewRelogradeBrand;
use Haida\FilamentRelograde\Support\RelogradeAuthorization;
use Haida\FilamentRelograde\Support\RelogradeLabels;
use Haida\FilamentRelograde\Support\RelogradeNotifier;
use Illuminate\Database\Eloquent\Builder;

class RelogradeBrandResource extends Resource
{
    protected static ?string $model = RelogradeBrand::class;

    protected static ?string $modelLabel = 'برند رلوگرید';

    protected static ?string $pluralModelLabel = 'برندهای رلوگرید';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'برندها';

    protected static string|\UnitEnum|null $navigationGroup = 'رلوگرید';

    protected static ?int $navigationSort = 2;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('connection.name')->label('اتصال')->toggleable(),
                TextColumn::make('brand_name')->label('برند')->searchable(),
                TextColumn::make('slug')->label('اسلاگ')->searchable(),
                TextColumn::make('category')->label('دسته‌بندی')->badge()->toggleable(),
                TextColumn::make('redeem_type')->label('نوع بازخرید')->badge()->toggleable(),
                TextColumn::make('options_count')->label('گزینه‌ها')->counts('options'),
                TextColumn::make('products_count')->label('محصولات')->getStateUsing(function (RelogradeBrand $record) {
                    return $record->products()->count();
                }),
                TextColumn::make('synced_at')->label('آخرین همگام‌سازی')->jalaliDateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('connection_id')
                    ->label('اتصال')
                    ->options(fn () => RelogradeConnection::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                SelectFilter::make('category')
                    ->label('دسته‌بندی')
                    ->options(fn () => RelogradeBrand::query()->whereNotNull('category')->distinct()->pluck('category', 'category')->toArray())
                    ->searchable(),
                SelectFilter::make('redeem_type')
                    ->label('نوع بازخرید')
                    ->options(fn () => RelogradeBrand::query()->whereNotNull('redeem_type')->distinct()->pluck('redeem_type', 'redeem_type')->toArray())
                    ->searchable(),
                SelectFilter::make('currency')
                    ->label('ارز')
                    ->options(fn () => RelogradeProduct::query()->whereNotNull('face_value_currency')->distinct()->pluck('face_value_currency', 'face_value_currency')->toArray())
                    ->searchable()
                    ->query(function (Builder $query, array $data) {
                        if (! $data['value']) {
                            return $query;
                        }

                        return $query->whereHas('products', function (Builder $productQuery) use ($data) {
                            $productQuery->where('face_value_currency', $data['value']);
                        });
                    }),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
            ])
            ->headerActions([
                Action::make('sync')
                    ->label('همگام‌سازی برندها')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn () => RelogradeAuthorization::can('sync'))
                    ->action(function () {
                        $connection = RelogradeConnection::query()->default()->first();
                        if (! $connection) {
                            RelogradeNotifier::error(new \RuntimeException('اتصال پیش‌فرض یافت نشد.'), 'اتصال پیش‌فرض یافت نشد.');

                            return;
                        }

                        SyncBrandsJob::dispatch($connection->getKey(), true);
                        RelogradeNotifier::success('همگام‌سازی برندها در صف قرار گرفت.');
                    }),
            ])
            ->defaultSort('synced_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRelogradeBrands::route('/'),
            'view' => ViewRelogradeBrand::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('برند')
                    ->schema([
                        TextEntry::make('brand_name')->label('برند'),
                        TextEntry::make('slug')->label('اسلاگ'),
                        TextEntry::make('category')->label('دسته‌بندی'),
                        TextEntry::make('redeem_type')->label('نوع بازخرید'),
                        TextEntry::make('synced_at')->label('آخرین همگام‌سازی')->jalaliDateTime(),
                    ])
                    ->columns(2),
                Section::make('گزینه‌ها')
                    ->schema([
                        RepeatableEntry::make('options')
                            ->schema([
                                TextEntry::make('redeem_value')->label('مقدار بازخرید'),
                                TextEntry::make('raw_json.products')
                                    ->label('محصولات')
                                    ->formatStateUsing(fn ($state) => is_array($state) ? count($state) : 0),
                            ])
                            ->columns(2),
                    ]),
                Section::make('محصولات')
                    ->schema([
                        RepeatableEntry::make('products')
                            ->schema([
                                TextEntry::make('name')->label('نام'),
                                TextEntry::make('slug')->label('اسلاگ'),
                                TextEntry::make('face_value_currency')->label('ارز'),
                                TextEntry::make('is_variable_product')
                                    ->label('متغیر')
                                    ->formatStateUsing(fn ($state) => RelogradeLabels::boolean((bool) $state)),
                                TextEntry::make('is_stocked')
                                    ->label('موجود')
                                    ->formatStateUsing(fn ($state) => RelogradeLabels::boolean((bool) $state)),
                            ])
                            ->columns(3),
                    ]),
            ]);
    }
}
