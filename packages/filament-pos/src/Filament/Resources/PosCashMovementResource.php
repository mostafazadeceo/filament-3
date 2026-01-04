<?php

namespace Haida\FilamentPos\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPos\Filament\Resources\PosCashMovementResource\Pages\CreatePosCashMovement;
use Haida\FilamentPos\Filament\Resources\PosCashMovementResource\Pages\EditPosCashMovement;
use Haida\FilamentPos\Filament\Resources\PosCashMovementResource\Pages\ListPosCashMovements;
use Haida\FilamentPos\Models\PosCashMovement;
use Haida\FilamentPos\Models\PosCashierSession;
use Illuminate\Database\Eloquent\Model;

class PosCashMovementResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = PosCashMovement::class;

    protected static ?string $modelLabel = 'جابجایی نقدی';

    protected static ?string $pluralModelLabel = 'جابجایی‌های نقدی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string|\UnitEnum|null $navigationGroup = 'پوز';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['pos.use', 'pos.manage_cash']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['pos.use', 'pos.manage_cash'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('pos.manage_cash');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('pos.manage_cash', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('pos.manage_cash', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('session_id')
                    ->label('شیفت')
                    ->options(fn () => PosCashierSession::query()->pluck('id', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'open_float' => 'موجودی اولیه',
                        'cash_drop' => 'برداشت صندوق',
                        'pay_in' => 'واریز',
                        'pay_out' => 'پرداخت',
                        'close_reconcile' => 'بستن صندوق',
                    ])
                    ->required(),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->required(),
                TextInput::make('reason')
                    ->label('علت')
                    ->maxLength(255)
                    ->nullable(),
                DateTimePicker::make('recorded_at')
                    ->label('زمان ثبت')
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('session_id')
                    ->label('شیفت')
                    ->sortable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('مبلغ'),
                TextColumn::make('recorded_at')
                    ->label('زمان ثبت')
                    ->jalaliDateTime(),
                TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosCashMovements::route('/'),
            'create' => CreatePosCashMovement::route('/create'),
            'edit' => EditPosCashMovement::route('/{record}/edit'),
        ];
    }
}
