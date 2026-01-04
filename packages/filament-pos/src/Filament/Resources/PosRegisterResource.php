<?php

namespace Haida\FilamentPos\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPos\Filament\Resources\PosRegisterResource\Pages\CreatePosRegister;
use Haida\FilamentPos\Filament\Resources\PosRegisterResource\Pages\EditPosRegister;
use Haida\FilamentPos\Filament\Resources\PosRegisterResource\Pages\ListPosRegisters;
use Haida\FilamentPos\Models\PosRegister;
use Haida\FilamentPos\Models\PosStore;
use Illuminate\Database\Eloquent\Model;

class PosRegisterResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = PosRegister::class;

    protected static ?string $modelLabel = 'صندوق';

    protected static ?string $pluralModelLabel = 'صندوق‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static string|\UnitEnum|null $navigationGroup = 'پوز';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['pos.view', 'pos.use', 'pos.manage_register'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('pos.manage_register');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('pos.manage_register', IamAuthorization::resolveTenantFromRecord($record));
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
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64)
                    ->nullable(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('store.name')
                    ->label('فروشگاه')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('last_opened_at')
                    ->label('آخرین بازگشایی')
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
            'index' => ListPosRegisters::route('/'),
            'create' => CreatePosRegister::route('/create'),
            'edit' => EditPosRegister::route('/{record}/edit'),
        ];
    }
}
