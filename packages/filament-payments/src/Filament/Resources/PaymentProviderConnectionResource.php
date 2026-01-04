<?php

namespace Haida\FilamentPayments\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPayments\Filament\Resources\PaymentProviderConnectionResource\Pages\CreatePaymentProviderConnection;
use Haida\FilamentPayments\Filament\Resources\PaymentProviderConnectionResource\Pages\EditPaymentProviderConnection;
use Haida\FilamentPayments\Filament\Resources\PaymentProviderConnectionResource\Pages\ListPaymentProviderConnections;
use Haida\FilamentPayments\Models\PaymentProviderConnection;

class PaymentProviderConnectionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payments';

    protected static ?string $model = PaymentProviderConnection::class;

    protected static ?string $modelLabel = 'اتصال درگاه';

    protected static ?string $pluralModelLabel = 'اتصال‌های درگاه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'پرداخت‌ها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('provider_key')
                    ->label('درگاه')
                    ->options(fn () => collect(array_keys((array) config('filament-payments.providers', [])))
                        ->mapWithKeys(fn ($key) => [$key => $key])
                        ->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('display_name')
                    ->label('نام نمایشی')
                    ->maxLength(255)
                    ->nullable(),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                Textarea::make('credentials')
                    ->label('اعتبارنامه‌ها (JSON)')
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
                TextColumn::make('provider_key')
                    ->label('درگاه')
                    ->searchable(),
                TextColumn::make('display_name')
                    ->label('نام نمایشی')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('فعال')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentProviderConnections::route('/'),
            'create' => CreatePaymentProviderConnection::route('/create'),
            'edit' => EditPaymentProviderConnection::route('/{record}/edit'),
        ];
    }
}
