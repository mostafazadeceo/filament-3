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
use Haida\FilamentPayments\Filament\Resources\PaymentIntentResource\Pages\CreatePaymentIntent;
use Haida\FilamentPayments\Filament\Resources\PaymentIntentResource\Pages\EditPaymentIntent;
use Haida\FilamentPayments\Filament\Resources\PaymentIntentResource\Pages\ListPaymentIntents;
use Haida\FilamentPayments\Models\PaymentIntent;

class PaymentIntentResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payments';

    protected static ?string $model = PaymentIntent::class;

    protected static ?string $modelLabel = 'درخواست پرداخت';

    protected static ?string $pluralModelLabel = 'درخواست‌های پرداخت';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static string|\UnitEnum|null $navigationGroup = 'پرداخت‌ها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('provider')
                    ->label('درگاه')
                    ->options(fn () => collect(array_keys((array) config('filament-payments.providers', [])))
                        ->mapWithKeys(fn ($key) => [$key => $key])
                        ->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('filament-payments.defaults.currency', 'IRR'))
                    ->maxLength(8),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'confirmed' => 'تایید شده',
                        'failed' => 'ناموفق',
                        'cancelled' => 'لغو شده',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('provider_reference')
                    ->label('شناسه درگاه')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('redirect_url')
                    ->label('لینک انتقال')
                    ->maxLength(1024)
                    ->nullable(),
                TextInput::make('reference_type')
                    ->label('نوع مرجع')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('reference_id')
                    ->label('شناسه مرجع')
                    ->numeric()
                    ->nullable(),
                TextInput::make('idempotency_key')
                    ->label('کلید آیدمپوتنسی')
                    ->maxLength(255)
                    ->nullable(),
                DateTimePicker::make('expires_at')
                    ->label('انقضا')
                    ->nullable(),
                Textarea::make('metadata')
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
                TextColumn::make('provider')
                    ->label('درگاه')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('مبلغ'),
                TextColumn::make('currency')
                    ->label('ارز'),
                TextColumn::make('provider_reference')
                    ->label('شناسه درگاه')
                    ->searchable(),
                TextColumn::make('reference_type')
                    ->label('نوع مرجع')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference_id')
                    ->label('شناسه مرجع')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentIntents::route('/'),
            'create' => CreatePaymentIntent::route('/create'),
            'edit' => EditPaymentIntent::route('/{record}/edit'),
        ];
    }
}
