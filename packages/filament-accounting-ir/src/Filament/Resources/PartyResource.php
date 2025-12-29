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
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\PartyResource\Pages\CreateParty;
use Vendor\FilamentAccountingIr\Filament\Resources\PartyResource\Pages\EditParty;
use Vendor\FilamentAccountingIr\Filament\Resources\PartyResource\Pages\ListParties;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\Party;

class PartyResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = Party::class;

    protected static ?string $modelLabel = 'شخص';

    protected static ?string $pluralModelLabel = 'اشخاص';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'اشخاص';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاعات پایه';

    protected static ?int $navigationSort = 1;

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
                Select::make('party_type')
                    ->label('نوع')
                    ->options([
                        'customer' => 'مشتری',
                        'supplier' => 'تامین‌کننده',
                        'employee' => 'پرسنل',
                    ])
                    ->default('customer'),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('legal_name')
                    ->label('نام حقوقی')
                    ->maxLength(255),
                TextInput::make('national_id')
                    ->label('شناسه ملی/کد ملی')
                    ->maxLength(32),
                TextInput::make('economic_code')
                    ->label('کد اقتصادی')
                    ->maxLength(32),
                TextInput::make('registration_number')
                    ->label('شماره ثبت')
                    ->maxLength(32),
                TextInput::make('phone')
                    ->label('تلفن')
                    ->maxLength(32),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->maxLength(255),
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
                TextColumn::make('party_type')->label('نوع')->badge(),
                TextColumn::make('national_id')->label('شناسه')->searchable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                ToggleColumn::make('is_active')->label('فعال'),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListParties::route('/'),
            'create' => CreateParty::route('/create'),
            'edit' => EditParty::route('/{record}/edit'),
        ];
    }
}
