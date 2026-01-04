<?php

namespace Haida\FilamentPayments\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPayments\Filament\Resources\PaymentRefundResource\Pages\CreatePaymentRefund;
use Haida\FilamentPayments\Filament\Resources\PaymentRefundResource\Pages\EditPaymentRefund;
use Haida\FilamentPayments\Filament\Resources\PaymentRefundResource\Pages\ListPaymentRefunds;
use Haida\FilamentPayments\Models\PaymentIntent;
use Haida\FilamentPayments\Models\PaymentRefund;

class PaymentRefundResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payments';

    protected static ?string $model = PaymentRefund::class;

    protected static ?string $modelLabel = 'بازپرداخت';

    protected static ?string $pluralModelLabel = 'بازپرداخت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-left';

    protected static string|\UnitEnum|null $navigationGroup = 'پرداخت‌ها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('payment_intent_id')
                    ->label('درخواست پرداخت')
                    ->relationship('intent', 'id')
                    ->getOptionLabelFromRecordUsing(fn (PaymentIntent $record) => sprintf('#%s', $record->getKey()))
                    ->searchable()
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'processed' => 'پرداخت شد',
                        'failed' => 'ناموفق',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('filament-payments.defaults.currency', 'IRR'))
                    ->maxLength(8),
                TextInput::make('provider')
                    ->label('درگاه')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('reference')
                    ->label('شناسه درگاه')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('idempotency_key')
                    ->label('کلید آیدمپوتنسی')
                    ->maxLength(255)
                    ->nullable(),
                DateTimePicker::make('processed_at')
                    ->label('زمان پردازش')
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
                TextColumn::make('id')
                    ->label('شناسه')
                    ->sortable(),
                TextColumn::make('payment_intent_id')
                    ->label('درخواست پرداخت')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('مبلغ'),
                TextColumn::make('currency')
                    ->label('ارز'),
                TextColumn::make('provider')
                    ->label('درگاه'),
                TextColumn::make('processed_at')
                    ->label('پردازش')
                    ->jalaliDateTime(),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentRefunds::route('/'),
            'create' => CreatePaymentRefund::route('/create'),
            'edit' => EditPaymentRefund::route('/{record}/edit'),
        ];
    }
}
