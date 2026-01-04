<?php

namespace Haida\FilamentPayments\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPayments\Filament\Resources\PaymentReconciliationResource\Pages\CreatePaymentReconciliation;
use Haida\FilamentPayments\Filament\Resources\PaymentReconciliationResource\Pages\EditPaymentReconciliation;
use Haida\FilamentPayments\Filament\Resources\PaymentReconciliationResource\Pages\ListPaymentReconciliations;
use Haida\FilamentPayments\Models\PaymentReconciliation;

class PaymentReconciliationResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payments';

    protected static ?string $model = PaymentReconciliation::class;

    protected static ?string $modelLabel = 'تطبیق پرداخت';

    protected static ?string $pluralModelLabel = 'تطبیق‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

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
                    ->nullable(),
                DateTimePicker::make('period_start')
                    ->label('شروع دوره')
                    ->nullable(),
                DateTimePicker::make('period_end')
                    ->label('پایان دوره')
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'pending' => 'در انتظار',
                        'completed' => 'تکمیل شده',
                        'failed' => 'ناموفق',
                    ])
                    ->default('pending')
                    ->required(),
                Textarea::make('summary')
                    ->label('خلاصه (JSON)')
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
                TextColumn::make('provider')
                    ->label('درگاه')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('period_start')
                    ->label('شروع')
                    ->jalaliDateTime(),
                TextColumn::make('period_end')
                    ->label('پایان')
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
            'index' => ListPaymentReconciliations::route('/'),
            'create' => CreatePaymentReconciliation::route('/create'),
            'edit' => EditPaymentReconciliation::route('/{record}/edit'),
        ];
    }
}
