<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanyResource\Pages\CreateAccountingCompany;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanyResource\Pages\EditAccountingCompany;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanyResource\Pages\ListAccountingCompanies;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class AccountingCompanyResource extends Resource
{
    use InteractsWithTenant;

    protected static ?string $model = AccountingCompany::class;

    protected static ?string $modelLabel = 'شرکت';

    protected static ?string $pluralModelLabel = 'شرکت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'شرکت‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'هسته حسابداری';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                self::tenantSelect(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('legal_name')
                    ->label('نام حقوقی')
                    ->maxLength(255),
                TextInput::make('national_id')
                    ->label('شناسه ملی')
                    ->maxLength(32),
                TextInput::make('economic_code')
                    ->label('کد اقتصادی')
                    ->maxLength(32),
                TextInput::make('registration_number')
                    ->label('شماره ثبت')
                    ->maxLength(32),
                TextInput::make('vat_number')
                    ->label('شماره مالیات بر ارزش افزوده')
                    ->maxLength(64),
                TextInput::make('timezone')
                    ->label('منطقه زمانی')
                    ->default('Asia/Tehran')
                    ->maxLength(64),
                TextInput::make('base_currency')
                    ->label('ارز پایه')
                    ->default('IRR')
                    ->maxLength(8),
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
                TextColumn::make('national_id')->label('شناسه ملی')->searchable(),
                TextColumn::make('economic_code')->label('کد اقتصادی')->searchable(),
                ToggleColumn::make('is_active')->label('فعال'),
                TextColumn::make('created_at')->label('ایجاد')->jalaliDateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountingCompanies::route('/'),
            'create' => CreateAccountingCompany::route('/create'),
            'edit' => EditAccountingCompany::route('/{record}/edit'),
        ];
    }
}
