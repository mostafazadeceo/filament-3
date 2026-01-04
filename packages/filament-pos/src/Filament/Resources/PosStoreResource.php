<?php

namespace Haida\FilamentPos\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPos\Filament\Resources\PosStoreResource\Pages\CreatePosStore;
use Haida\FilamentPos\Filament\Resources\PosStoreResource\Pages\EditPosStore;
use Haida\FilamentPos\Filament\Resources\PosStoreResource\Pages\ListPosStores;
use Haida\FilamentPos\Models\PosStore;
use Illuminate\Database\Eloquent\Model;

class PosStoreResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = PosStore::class;

    protected static ?string $modelLabel = 'شعبه/فروشگاه';

    protected static ?string $pluralModelLabel = 'شعبه‌ها/فروشگاه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

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
                TextInput::make('currency')
                    ->label('ارز')
                    ->default(fn () => config('filament-pos.defaults.currency', 'IRR'))
                    ->maxLength(8),
                TextInput::make('timezone')
                    ->label('منطقه زمانی')
                    ->maxLength(64)
                    ->nullable(),
                Textarea::make('address')
                    ->label('آدرس')
                    ->rows(3)
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
                TextColumn::make('name')
                    ->label('نام')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('کد'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('currency')
                    ->label('ارز'),
                TextColumn::make('updated_at')
                    ->label('بروزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosStores::route('/'),
            'create' => CreatePosStore::route('/create'),
            'edit' => EditPosStore::route('/{record}/edit'),
        ];
    }
}
