<?php

namespace Haida\FilamentCryptoCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoLedgerResource\Pages\ListCryptoLedgers;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoLedgerResource\Pages\ViewCryptoLedger;
use Haida\FilamentCryptoCore\Filament\Resources\CryptoLedgerResource\RelationManagers\CryptoLedgerEntriesRelationManager;
use Haida\FilamentCryptoCore\Models\CryptoLedger;
use Illuminate\Database\Eloquent\Model;

class CryptoLedgerResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'crypto.ledgers';

    protected static ?string $model = CryptoLedger::class;

    protected static ?string $modelLabel = 'سند دفترکل';

    protected static ?string $pluralModelLabel = 'اسناد دفترکل';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|\UnitEnum|null $navigationGroup = 'n&n';

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
                TextInput::make('ref_type')
                    ->label('نوع مرجع')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('ref_id')
                    ->label('شناسه مرجع')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('occurred_at')
                    ->label('زمان')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('meta')
                    ->label('متا (JSON)')
                    ->rows(4)
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ref_type')
                    ->label('نوع مرجع')
                    ->searchable(),
                TextColumn::make('ref_id')
                    ->label('شناسه مرجع')
                    ->searchable(),
                TextColumn::make('occurred_at')
                    ->label('زمان')
                    ->jalaliDateTime(),
                TextColumn::make('entries_count')
                    ->label('آرتیکل‌ها')
                    ->counts('entries'),
                TextColumn::make('updated_at')
                    ->label('به‌روزرسانی')
                    ->jalaliDateTime(),
            ])
            ->defaultSort('occurred_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            CryptoLedgerEntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCryptoLedgers::route('/'),
            'view' => ViewCryptoLedger::route('/{record}'),
        ];
    }
}
