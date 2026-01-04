<?php

namespace Haida\CommerceOrders\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource\Pages\CreateCommerceOrderReturn;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource\Pages\EditCommerceOrderReturn;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource\Pages\ListCommerceOrderReturns;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderReturnResource\RelationManagers\OrderReturnItemsRelationManager;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderReturn;

class CommerceOrderReturnResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.order.return';

    protected static ?string $model = OrderReturn::class;

    protected static ?string $modelLabel = 'مرجوعی';

    protected static ?string $pluralModelLabel = 'مرجوعی‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static string|\UnitEnum|null $navigationGroup = 'فروشگاه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('order_id')
                    ->label('سفارش')
                    ->options(fn () => Order::query()
                        ->get()
                        ->mapWithKeys(fn (Order $order) => [
                            $order->getKey() => $order->number ?: ('#'.$order->getKey()),
                        ])
                        ->toArray())
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'requested' => 'درخواست شده',
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                        'received' => 'دریافت شد',
                        'refunded' => 'بازپرداخت شد',
                    ])
                    ->default('requested')
                    ->required(),
                TextInput::make('reason')
                    ->label('دلیل')
                    ->maxLength(255)
                    ->nullable(),
                Textarea::make('notes')
                    ->label('یادداشت')
                    ->rows(3)
                    ->nullable(),
                DateTimePicker::make('requested_at')
                    ->label('زمان درخواست'),
                DateTimePicker::make('approved_at')
                    ->label('زمان تایید'),
                DateTimePicker::make('rejected_at')
                    ->label('زمان رد'),
                DateTimePicker::make('received_at')
                    ->label('زمان دریافت'),
                DateTimePicker::make('refunded_at')
                    ->label('زمان بازپرداخت'),
                Textarea::make('meta')
                    ->label('متادیتا (JSON)')
                    ->rows(3)
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
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order.number')->label('شماره سفارش')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('reason')->label('دلیل'),
                TextColumn::make('requested_at')->label('درخواست')->jalaliDateTime(),
                TextColumn::make('updated_at')->label('بروزرسانی')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'requested' => 'درخواست شده',
                        'approved' => 'تایید شده',
                        'rejected' => 'رد شده',
                        'received' => 'دریافت شد',
                        'refunded' => 'بازپرداخت شد',
                    ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            OrderReturnItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceOrderReturns::route('/'),
            'create' => CreateCommerceOrderReturn::route('/create'),
            'edit' => EditCommerceOrderReturn::route('/{record}/edit'),
        ];
    }
}
