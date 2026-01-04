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
use Haida\FilamentPos\Filament\Resources\PosCashierSessionResource\Pages\CreatePosCashierSession;
use Haida\FilamentPos\Filament\Resources\PosCashierSessionResource\Pages\EditPosCashierSession;
use Haida\FilamentPos\Filament\Resources\PosCashierSessionResource\Pages\ListPosCashierSessions;
use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosRegister;
use Haida\FilamentPos\Models\PosStore;
use Illuminate\Database\Eloquent\Model;

class PosCashierSessionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = PosCashierSession::class;

    protected static ?string $modelLabel = 'شیفت صندوق';

    protected static ?string $pluralModelLabel = 'شیفت‌های صندوق';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

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
                Select::make('device_id')
                    ->label('دستگاه')
                    ->options(fn () => PosDevice::query()->pluck('device_uid', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'open' => 'باز',
                        'closed' => 'بسته',
                    ])
                    ->default('open')
                    ->required(),
                DateTimePicker::make('opened_at')
                    ->label('شروع')
                    ->nullable(),
                DateTimePicker::make('closed_at')
                    ->label('پایان')
                    ->nullable(),
                TextInput::make('opening_float')
                    ->label('موجودی اولیه')
                    ->numeric()
                    ->default(0),
                TextInput::make('closing_cash')
                    ->label('موجودی بسته‌شدن')
                    ->numeric()
                    ->default(0),
                TextInput::make('expected_cash')
                    ->label('موجودی مورد انتظار')
                    ->numeric()
                    ->default(0),
                TextInput::make('variance')
                    ->label('انحراف')
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->label('یادداشت')
                    ->rows(3)
                    ->nullable(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('register.name')
                    ->label('صندوق'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('opened_at')
                    ->label('شروع')
                    ->jalaliDateTime(),
                TextColumn::make('closed_at')
                    ->label('پایان')
                    ->jalaliDateTime(),
                TextColumn::make('expected_cash')
                    ->label('موجودی مورد انتظار'),
                TextColumn::make('variance')
                    ->label('انحراف'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosCashierSessions::route('/'),
            'create' => CreatePosCashierSession::route('/create'),
            'edit' => EditPosCashierSession::route('/{record}/edit'),
        ];
    }
}
