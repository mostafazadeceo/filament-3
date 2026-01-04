<?php

namespace Haida\FilamentPayments\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPayments\Filament\Resources\PaymentWebhookEventResource\Pages\ListPaymentWebhookEvents;
use Haida\FilamentPayments\Filament\Resources\PaymentWebhookEventResource\Pages\ViewPaymentWebhookEvent;
use Haida\FilamentPayments\Models\PaymentWebhookEvent;
use Illuminate\Database\Eloquent\Model;

class PaymentWebhookEventResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payments.webhooks';

    protected static ?string $model = PaymentWebhookEvent::class;

    protected static ?string $modelLabel = 'وبهوک پرداخت';

    protected static ?string $pluralModelLabel = 'وبهوک‌های پرداخت';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected static string|\UnitEnum|null $navigationGroup = 'پرداخت‌ها';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('provider')
                    ->label('درگاه')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('event_type')
                    ->label('نوع رویداد')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('external_id')
                    ->label('شناسه بیرونی')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('status')
                    ->label('وضعیت')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('signature_valid')
                    ->label('امضا معتبر')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('received_at')
                    ->label('دریافت')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('processed_at')
                    ->label('پردازش')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('headers')
                    ->label('هدرها (JSON)')
                    ->rows(4)
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    }),
                Textarea::make('payload')
                    ->label('بدنه (JSON)')
                    ->rows(6)
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
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
                TextColumn::make('event_type')
                    ->label('نوع رویداد')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                IconColumn::make('signature_valid')
                    ->label('امضا معتبر')
                    ->boolean(),
                TextColumn::make('received_at')
                    ->label('دریافت')
                    ->jalaliDateTime(),
                TextColumn::make('processed_at')
                    ->label('پردازش')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPaymentWebhookEvents::route('/'),
            'view' => ViewPaymentWebhookEvent::route('/{record}'),
        ];
    }
}
