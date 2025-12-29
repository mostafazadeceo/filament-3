<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountPlanResource\Pages\CreateAccountPlan;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountPlanResource\Pages\EditAccountPlan;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountPlanResource\Pages\ListAccountPlans;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountPlan;

class AccountPlanResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = AccountPlan::class;

    protected static ?string $modelLabel = 'پلن کدینگ';

    protected static ?string $pluralModelLabel = 'پلن‌های کدینگ';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'پلن‌های کدینگ';

    protected static string|\UnitEnum|null $navigationGroup = 'هسته حسابداری';

    protected static ?int $navigationSort = 3;

    protected static array $eagerLoad = ['company'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('industry')
                    ->label('صنعت')
                    ->maxLength(255),
                Toggle::make('is_default')
                    ->label('پیش‌فرض')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('industry')->label('صنعت')->searchable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                ToggleColumn::make('is_default')->label('پیش‌فرض'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountPlans::route('/'),
            'create' => CreateAccountPlan::route('/create'),
            'edit' => EditAccountPlan::route('/{record}/edit'),
        ];
    }
}
