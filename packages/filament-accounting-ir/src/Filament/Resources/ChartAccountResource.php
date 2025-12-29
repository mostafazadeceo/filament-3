<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\ChartAccountResource\Pages\CreateChartAccount;
use Vendor\FilamentAccountingIr\Filament\Resources\ChartAccountResource\Pages\EditChartAccount;
use Vendor\FilamentAccountingIr\Filament\Resources\ChartAccountResource\Pages\ListChartAccounts;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountPlan;
use Vendor\FilamentAccountingIr\Models\AccountType;
use Vendor\FilamentAccountingIr\Models\ChartAccount;

class ChartAccountResource extends Resource
{
    use InteractsWithTenant;

    protected static ?string $model = ChartAccount::class;

    protected static ?string $modelLabel = 'حساب';

    protected static ?string $pluralModelLabel = 'کدینگ حساب‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'کدینگ حساب‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'هسته حسابداری';

    protected static ?int $navigationSort = 4;

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
                Select::make('plan_id')
                    ->label('پلن کدینگ')
                    ->options(fn () => AccountPlan::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('type_id')
                    ->label('نوع حساب')
                    ->options(fn () => AccountType::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('parent_id')
                    ->label('حساب والد')
                    ->options(fn () => ChartAccount::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('code')
                    ->label('کد')
                    ->required()
                    ->maxLength(64),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('level')
                    ->label('سطح')
                    ->numeric()
                    ->default(1),
                TagsInput::make('requires_dimensions')
                    ->label('ابعاد اجباری')
                    ->placeholder('مثلاً: cost_center, project'),
                Toggle::make('is_postable')
                    ->label('قابل ثبت')
                    ->default(false),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('کد')->searchable()->sortable(),
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('type.name')->label('نوع')->sortable(),
                TextColumn::make('level')->label('سطح')->sortable(),
                ToggleColumn::make('is_postable')->label('قابل ثبت'),
                ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->defaultSort('code');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChartAccounts::route('/'),
            'create' => CreateChartAccount::route('/create'),
            'edit' => EditChartAccount::route('/{record}/edit'),
        ];
    }
}
