<?php

declare(strict_types=1);

namespace Haida\FilamentProvidersEsimGo\Resources;

use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\ProvidersEsimGoCore\Exceptions\EsimGoApiException;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoOrderResource\Pages\ListEsimGoOrders;
use Haida\FilamentProvidersEsimGo\Resources\EsimGoOrderResource\Pages\ViewEsimGoOrder;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Haida\ProvidersEsimGoCore\Services\EsimGoOrderService;
use Haida\ProvidersEsimGoCore\Support\EsimGoLabels;
use Throwable;

class EsimGoOrderResource extends IamResource
{
    protected static ?string $model = EsimGoOrder::class;

    protected static ?string $permissionPrefix = 'esim_go.order';

    protected static ?string $modelLabel = 'سفارش eSIM Go';

    protected static ?string $pluralModelLabel = 'سفارش‌های eSIM Go';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'سفارش‌ها';

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider_reference')->label('مرجع Provider')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge()->formatStateUsing(fn ($state) => EsimGoLabels::orderStatus($state)),
                TextColumn::make('total')->label('مبلغ'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->actions([
                Action::make('refresh')
                    ->label('به‌روزرسانی')
                    ->icon('heroicon-o-arrow-path')
                    ->disabled(fn (EsimGoOrder $record) => ! $record->provider_reference)
                    ->action(function (EsimGoOrder $record, EsimGoOrderService $service) {
                        try {
                            $service->refreshAssignments($record);

                            Notification::make()
                                ->title('به‌روزرسانی انجام شد')
                                ->success()
                                ->send();
                        } catch (EsimGoApiException $exception) {
                            Notification::make()
                                ->title('ارتباط با eSIM Go ناموفق بود')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        } catch (Throwable $exception) {
                            Notification::make()
                                ->title('به‌روزرسانی ناموفق بود')
                                ->body('خطای غیرمنتظره رخ داد.')
                                ->danger()
                                ->send();
                        }
                    }),
                ViewAction::make()->label('مشاهده'),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEsimGoOrders::route('/'),
            'view' => ViewEsimGoOrder::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('جزئیات سفارش')
                ->schema([
                    TextEntry::make('provider_reference')->label('مرجع Provider'),
                    TextEntry::make('status')
                        ->label('وضعیت')
                        ->badge()
                        ->formatStateUsing(fn ($state) => EsimGoLabels::orderStatus($state)),
                    TextEntry::make('status_message')
                        ->label('پیام وضعیت')
                        ->columnSpanFull(),
                    TextEntry::make('total')
                        ->label('مبلغ')
                        ->formatStateUsing(fn ($state, EsimGoOrder $record) => $record->total . ' ' . $record->currency),
                    TextEntry::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime(),
                ])
                ->columns(3),
        ]);
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
