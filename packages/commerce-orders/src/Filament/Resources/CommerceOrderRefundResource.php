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
use Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource\Pages\CreateCommerceOrderRefund;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource\Pages\EditCommerceOrderRefund;
use Haida\CommerceOrders\Filament\Resources\CommerceOrderRefundResource\Pages\ListCommerceOrderRefunds;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderPayment;
use Haida\CommerceOrders\Models\OrderRefund;
use Haida\CommerceOrders\Models\OrderReturn;

class CommerceOrderRefundResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'commerce.order.refund';

    protected static ?string $model = OrderRefund::class;

    protected static ?string $modelLabel = 'بازپرداخت';

    protected static ?string $pluralModelLabel = 'بازپرداخت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-refund';

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
                Select::make('order_return_id')
                    ->label('مرجوعی')
                    ->options(fn () => OrderReturn::query()
                        ->get()
                        ->mapWithKeys(fn (OrderReturn $return) => [
                            $return->getKey() => 'RMA-'.$return->getKey(),
                        ])
                        ->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('order_payment_id')
                    ->label('پرداخت')
                    ->options(fn () => OrderPayment::query()
                        ->get()
                        ->mapWithKeys(fn (OrderPayment $payment) => [
                            $payment->getKey() => $payment->reference ?: ('#'.$payment->getKey()),
                        ])
                        ->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default('IRR')
                    ->maxLength(8),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'processed' => 'پردازش شد',
                        'failed' => 'ناموفق',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('provider')
                    ->label('درگاه')
                    ->maxLength(120)
                    ->nullable(),
                TextInput::make('reference')
                    ->label('کد پیگیری')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('reason')
                    ->label('دلیل')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('idempotency_key')
                    ->label('کلید یکتایی')
                    ->maxLength(255)
                    ->nullable(),
                DateTimePicker::make('processed_at')
                    ->label('زمان پردازش'),
                Textarea::make('notes')
                    ->label('یادداشت')
                    ->rows(3)
                    ->nullable(),
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
                TextColumn::make('amount')->label('مبلغ'),
                TextColumn::make('currency')->label('ارز'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('processed_at')->label('پردازش')->jalaliDateTime(),
                TextColumn::make('updated_at')->label('بروزرسانی')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'processed' => 'پردازش شد',
                        'failed' => 'ناموفق',
                    ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceOrderRefunds::route('/'),
            'create' => CreateCommerceOrderRefund::route('/create'),
            'edit' => EditCommerceOrderRefund::route('/{record}/edit'),
        ];
    }
}
