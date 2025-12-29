<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\ContractResource\Pages\CreateContract;
use Vendor\FilamentAccountingIr\Filament\Resources\ContractResource\Pages\EditContract;
use Vendor\FilamentAccountingIr\Filament\Resources\ContractResource\Pages\ListContracts;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\Contract;
use Vendor\FilamentAccountingIr\Models\Party;
use Vendor\FilamentAccountingIr\Models\Project;

class ContractResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = Contract::class;

    protected static ?string $modelLabel = 'قرارداد';

    protected static ?string $pluralModelLabel = 'قراردادها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'قراردادها';

    protected static string|\UnitEnum|null $navigationGroup = 'پروژه و پیمانکاری';

    protected static ?int $navigationSort = 2;

    protected static array $eagerLoad = ['project', 'party'];

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
                Select::make('project_id')
                    ->label('پروژه')
                    ->options(fn () => Project::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('party_id')
                    ->label('کارفرما')
                    ->options(fn () => Party::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('contract_no')
                    ->label('شماره قرارداد')
                    ->maxLength(64),
                TextInput::make('amount')
                    ->label('مبلغ')
                    ->numeric()
                    ->minValue(0),
                DatePicker::make('start_date')
                    ->label('شروع'),
                DatePicker::make('end_date')
                    ->label('پایان'),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'closed' => 'بسته',
                    ])
                    ->default('active'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contract_no')->label('شماره')->searchable()->sortable(),
                TextColumn::make('project.name')->label('پروژه')->sortable(),
                TextColumn::make('party.name')->label('کارفرما')->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('amount')->label('مبلغ')->numeric(decimalPlaces: 0),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContracts::route('/'),
            'create' => CreateContract::route('/create'),
            'edit' => EditContract::route('/{record}/edit'),
        ];
    }
}
