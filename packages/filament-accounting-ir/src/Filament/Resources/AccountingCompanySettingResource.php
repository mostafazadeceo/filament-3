<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanySettingResource\Pages\CreateAccountingCompanySetting;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanySettingResource\Pages\EditAccountingCompanySetting;
use Vendor\FilamentAccountingIr\Filament\Resources\AccountingCompanySettingResource\Pages\ListAccountingCompanySettings;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Models\ChartAccount;

class AccountingCompanySettingResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = AccountingCompanySetting::class;

    protected static ?string $modelLabel = 'تنظیمات شرکت';

    protected static ?string $pluralModelLabel = 'تنظیمات شرکت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'تنظیمات شرکت';

    protected static string|\UnitEnum|null $navigationGroup = 'تنظیمات';

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
                    ->required()
                    ->reactive()
                    ->helperText('برای هر شرکت تنها یک تنظیمات ثبت می‌شود.')
                    ->rule(function (Get $get, ?AccountingCompanySetting $record) {
                        $rule = Rule::unique('accounting_ir_company_settings', 'company_id');
                        $tenantId = $get('tenant_id');
                        if ($tenantId) {
                            $rule->where('tenant_id', $tenantId);
                        }
                        if ($record) {
                            $rule->ignore($record->getKey());
                        }

                        return $rule;
                    })
                    ->afterStateUpdated(function ($state, Set $set): void {
                        $set('posting_accounts.sales_revenue', null);
                        $set('posting_accounts.sales_tax', null);
                        $set('posting_accounts.accounts_receivable', null);
                        $set('posting_accounts.purchase_expense', null);
                        $set('posting_accounts.purchase_tax', null);
                        $set('posting_accounts.accounts_payable', null);
                        $set('posting_accounts.cash', null);
                        $set('posting_accounts.bank', null);
                    }),
                Section::make('حساب‌های پیش‌فرض')
                    ->description('این حساب‌ها برای ثبت اتومات اسناد فروش و خرید استفاده می‌شوند.')
                    ->schema([
                        self::accountSelect('sales_revenue', 'حساب فروش', 'الزامی برای ثبت سند فروش.')->required(),
                        self::accountSelect('accounts_receivable', 'حساب دریافتنی', 'الزامی برای ثبت سند فروش.')->required(),
                        self::accountSelect('sales_tax', 'مالیات و عوارض فروش', 'اختیاری؛ برای فروش‌های مشمول مالیات.'),
                        self::accountSelect('purchase_expense', 'حساب خرید', 'الزامی برای ثبت سند خرید.')->required(),
                        self::accountSelect('accounts_payable', 'حساب پرداختنی', 'الزامی برای ثبت سند خرید.')->required(),
                        self::accountSelect('purchase_tax', 'مالیات و عوارض خرید', 'اختیاری؛ برای خریدهای مشمول مالیات.'),
                        self::accountSelect('cash', 'صندوق', 'اختیاری؛ برای پرداخت‌های نقدی.'),
                        self::accountSelect('bank', 'بانک', 'اختیاری؛ برای پرداخت‌های بانکی.'),
                    ])
                    ->columns(2),
                Section::make('سیاست‌ها')
                    ->description('این سیاست‌ها بر ثبت اسناد و کنترل موجودی اثر می‌گذارند.')
                    ->schema([
                        Toggle::make('posting_requires_approval')
                            ->label('ثبت اتومات نیاز به تایید داشته باشد')
                            ->helperText('اگر خاموش باشد، سندها پس از ثبت اتومات بلافاصله قطعی می‌شوند.')
                            ->default(true),
                        Toggle::make('allow_negative_inventory')
                            ->label('اجازه موجودی منفی در انبار')
                            ->helperText('در صورت فعال بودن، منفی شدن موجودی برای این شرکت مجاز است.')
                            ->default(false),
                    ])
                    ->columns(2),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                ToggleColumn::make('posting_requires_approval')->label('نیاز به تایید'),
                ToggleColumn::make('allow_negative_inventory')->label('موجودی منفی'),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->jalaliDateTime()->sortable(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccountingCompanySettings::route('/'),
            'create' => CreateAccountingCompanySetting::route('/create'),
            'edit' => EditAccountingCompanySetting::route('/{record}/edit'),
        ];
    }

    protected static function accountSelect(string $key, string $label, ?string $helperText = null): Select
    {
        return Select::make("posting_accounts.{$key}")
            ->label($label)
            ->options(function (Get $get): array {
                $companyId = $get('company_id');
                if (! $companyId) {
                    return [];
                }

                return ChartAccount::query()
                    ->where('company_id', $companyId)
                    ->where('is_postable', true)
                    ->orderBy('code')
                    ->get()
                    ->mapWithKeys(fn (ChartAccount $account) => [
                        $account->getKey() => $account->code.' - '.$account->name,
                    ])
                    ->toArray();
            })
            ->searchable()
            ->preload()
            ->helperText($helperText ?? 'فقط حساب‌های قابل ثبت این شرکت نمایش داده می‌شود.')
            ->disabled(fn (Get $get): bool => ! $get('company_id'))
            ->rule(fn (Get $get) => Rule::exists('accounting_ir_chart_accounts', 'id')
                ->where('company_id', $get('company_id'))
                ->where('is_postable', true))
            ->nullable();
    }
}
