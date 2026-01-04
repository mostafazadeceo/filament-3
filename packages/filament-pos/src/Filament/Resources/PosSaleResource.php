<?php

namespace Haida\FilamentPos\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPos\Filament\Resources\PosSaleResource\Pages\CreatePosSale;
use Haida\FilamentPos\Filament\Resources\PosSaleResource\Pages\EditPosSale;
use Haida\FilamentPos\Filament\Resources\PosSaleResource\Pages\ListPosSales;
use Haida\FilamentPos\Filament\Resources\PosSaleResource\RelationManagers\PosSaleItemsRelationManager;
use Haida\FilamentPos\Filament\Resources\PosSaleResource\RelationManagers\PosSalePaymentsRelationManager;
use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosRegister;
use Haida\FilamentPos\Models\PosSale;
use Haida\FilamentPos\Models\PosStore;
use Illuminate\Database\Eloquent\Model;

class PosSaleResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = PosSale::class;

    protected static ?string $modelLabel = 'فروش پوز';

    protected static ?string $pluralModelLabel = 'فروش‌های پوز';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string|\UnitEnum|null $navigationGroup = 'پوز';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('pos.use');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('pos.use', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('pos.void', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('store_id')
                    ->label('فروشگاه')
                    ->options(fn () => PosStore::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('register_id')
                    ->label('صندوق')
                    ->options(fn () => PosRegister::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('session_id')
                    ->label('شیفت')
                    ->options(fn () => PosCashierSession::query()->pluck('id', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('device_id')
                    ->label('دستگاه')
                    ->options(fn () => PosDevice::query()->pluck('device_uid', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('receipt_no')
                    ->label('شماره رسید')
                    ->maxLength(255)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'open' => 'باز',
                        'paid' => 'پرداخت شد',
                        'voided' => 'باطل شد',
                        'refunded' => 'بازپرداخت',
                    ])
                    ->default('open')
                    ->required(),
                Select::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->options([
                        'pending' => 'در انتظار',
                        'partial' => 'جزئی',
                        'paid' => 'پرداخت شده',
                        'refunded' => 'بازپرداخت',
                    ])
                    ->default('pending')
                    ->required(),
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('filament-pos.defaults.currency', 'IRR'))
                    ->maxLength(8),
                TextInput::make('subtotal')
                    ->label('جمع جزء')
                    ->numeric()
                    ->default(0),
                TextInput::make('discount_total')
                    ->label('جمع تخفیف')
                    ->numeric()
                    ->default(0),
                TextInput::make('tax_total')
                    ->label('جمع مالیات')
                    ->numeric()
                    ->default(0),
                TextInput::make('total')
                    ->label('جمع کل')
                    ->numeric()
                    ->default(0),
                TextInput::make('source')
                    ->label('منبع')
                    ->maxLength(64)
                    ->default('pos'),
                TextInput::make('idempotency_key')
                    ->label('کلید آیدمپوتنسی')
                    ->maxLength(255)
                    ->nullable(),
                DateTimePicker::make('completed_at')
                    ->label('زمان تکمیل')
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
                TextColumn::make('receipt_no')
                    ->label('شماره رسید')
                    ->searchable(),
                TextColumn::make('store.name')
                    ->label('فروشگاه'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('payment_status')
                    ->label('وضعیت پرداخت')
                    ->badge(),
                TextColumn::make('total')
                    ->label('جمع کل'),
                TextColumn::make('currency')
                    ->label('ارز'),
                TextColumn::make('completed_at')
                    ->label('زمان تکمیل')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PosSaleItemsRelationManager::class,
            PosSalePaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosSales::route('/'),
            'create' => CreatePosSale::route('/create'),
            'edit' => EditPosSale::route('/{record}/edit'),
        ];
    }
}
