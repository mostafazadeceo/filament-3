<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingBranchResource\Pages\CreateAccountingBranch;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingBranchResource\Pages\EditAccountingBranch;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingBranchResource\Pages\ListAccountingBranches;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class AccountingBranchResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = AccountingBranch::class;

    protected static ?string $modelLabel = 'شعبه';

    protected static ?string $pluralModelLabel = 'شعبه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'شعبه‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'هسته حسابداری';

    protected static ?int $navigationSort = 2;

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
                TextInput::make('code')
                    ->label('کد شعبه')
                    ->maxLength(32),
                Textarea::make('address')
                    ->label('آدرس')
                    ->columnSpanFull(),
                TextInput::make('postal_code')
                    ->label('کد پستی')
                    ->maxLength(32),
                TextInput::make('phone')
                    ->label('تلفن')
                    ->maxLength(32),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                TextColumn::make('code')->label('کد')->searchable(),
                ToggleColumn::make('is_active')->label('فعال'),
                TextColumn::make('created_at')->label('ایجاد')->jalaliDateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountingBranches::route('/'),
            'create' => CreateAccountingBranch::route('/create'),
            'edit' => EditAccountingBranch::route('/{record}/edit'),
        ];
    }
}
