<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantSupplierResource\Pages\CreateRestaurantSupplier;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantSupplierResource\Pages\EditRestaurantSupplier;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantSupplierResource\Pages\ListRestaurantSuppliers;
use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\Party;

class RestaurantSupplierResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.supplier';

    protected static ?string $model = RestaurantSupplier::class;

    protected static ?string $modelLabel = 'تأمین‌کننده';

    protected static ?string $pluralModelLabel = 'تأمین‌کنندگان';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static string|\UnitEnum|null $navigationGroup = 'خرید';

    protected static array $eagerLoad = ['accountingParty'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->searchable()
                    ->required(),
                Select::make('accounting_party_id')
                    ->label('طرف حساب حسابداری')
                    ->options(fn (callable $get) => Party::query()
                        ->where('company_id', $get('company_id'))
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('code')
                    ->label('کد')
                    ->maxLength(64),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                TextInput::make('contact_name')
                    ->label('نام رابط')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('تلفن')
                    ->maxLength(32),
                TextInput::make('email')
                    ->label('ایمیل')
                    ->email()
                    ->maxLength(255),
                Textarea::make('address')
                    ->label('آدرس')
                    ->columnSpanFull(),
                TextInput::make('payment_terms')
                    ->label('شرایط پرداخت')
                    ->maxLength(255),
                TextInput::make('rating')
                    ->label('امتیاز')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('code')->label('کد')->searchable(),
                TextColumn::make('accountingParty.name')->label('طرف حساب'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('phone')->label('تلفن'),
                TextColumn::make('created_at')->label('ایجاد')->jalaliDateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRestaurantSuppliers::route('/'),
            'create' => CreateRestaurantSupplier::route('/create'),
            'edit' => EditRestaurantSupplier::route('/{record}/edit'),
        ];
    }
}
