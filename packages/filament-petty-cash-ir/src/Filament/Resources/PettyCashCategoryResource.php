<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCategoryResource\Pages\CreatePettyCashCategory;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCategoryResource\Pages\EditPettyCashCategory;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashCategoryResource\Pages\ListPettyCashCategories;
use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\ChartAccount;

class PettyCashCategoryResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.category';

    protected static ?string $model = PettyCashCategory::class;

    protected static ?string $modelLabel = 'دسته هزینه';

    protected static ?string $pluralModelLabel = 'دسته‌های هزینه';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                Select::make('status')
                    ->label('وضعیت')
                    ->options(PettyCashStatuses::fundOptions())
                    ->default(PettyCashStatuses::FUND_ACTIVE),
                Select::make('accounting_account_id')
                    ->label('حساب هزینه')
                    ->options(fn () => ChartAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('code')->label('کد'),
                TextColumn::make('status')->label('وضعیت')->badge(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashCategories::route('/'),
            'create' => CreatePettyCashCategory::route('/create'),
            'edit' => EditPettyCashCategory::route('/{record}/edit'),
        ];
    }
}
