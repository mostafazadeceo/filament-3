<?php

namespace Haida\TenancyDomains\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\SiteBuilderCore\Models\Site;
use Haida\TenancyDomains\Filament\Resources\SiteDomainResource\Pages\CreateSiteDomain;
use Haida\TenancyDomains\Filament\Resources\SiteDomainResource\Pages\EditSiteDomain;
use Haida\TenancyDomains\Filament\Resources\SiteDomainResource\Pages\ListSiteDomains;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Services\SiteDomainService;

class SiteDomainResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'site.domain';

    protected static ?string $model = SiteDomain::class;

    protected static ?string $modelLabel = 'دامنه';

    protected static ?string $pluralModelLabel = 'دامنه‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'سایت ساز';

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $tenant = TenantContext::getTenant();
        if ($tenant) {
            $query->where('tenant_id', $tenant->getKey());
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('site_id')
                    ->label('سایت')
                    ->options(fn () => Site::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                TextInput::make('host')
                    ->label('دامنه')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options([
                        'custom' => 'سفارشی',
                        'subdomain' => 'ساب دامنه',
                    ])
                    ->default('custom')
                    ->required(),
                Select::make('verification_method')
                    ->label('روش تایید')
                    ->options([
                        'txt' => 'TXT',
                        'cname' => 'CNAME',
                    ])
                    ->nullable(),
                TextInput::make('dns_token')
                    ->label('توکن DNS')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        SiteDomain::STATUS_PENDING => 'در انتظار',
                        SiteDomain::STATUS_VERIFIED => 'تایید شده',
                        SiteDomain::STATUS_FAILED => 'ناموفق',
                    ])
                    ->disabled()
                    ->dehydrated(false),
                Toggle::make('is_primary')
                    ->label('دامنه اصلی'),
                DateTimePicker::make('verified_at')
                    ->label('زمان تایید')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('last_checked_at')
                    ->label('آخرین بررسی')
                    ->disabled()
                    ->dehydrated(false),
                Select::make('tls_status')
                    ->label('وضعیت TLS')
                    ->options([
                        SiteDomain::TLS_STATUS_NOT_REQUESTED => 'ثبت نشده',
                        SiteDomain::TLS_STATUS_PENDING => 'در انتظار',
                        SiteDomain::TLS_STATUS_ISSUED => 'صادر شده',
                        SiteDomain::TLS_STATUS_FAILED => 'ناموفق',
                    ])
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('tls_provider')
                    ->label('ارائه‌دهنده TLS')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('tls_mode')
                    ->label('حالت TLS')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('tls_issued_at')
                    ->label('زمان صدور TLS')
                    ->disabled()
                    ->dehydrated(false),
                DateTimePicker::make('tls_expires_at')
                    ->label('انقضای TLS')
                    ->disabled()
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('host')->label('دامنه')->searchable(),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('status')->label('وضعیت')->badge(),
                IconColumn::make('is_primary')->label('اصلی')->boolean(),
                TextColumn::make('verified_at')->label('تایید')->jalaliDateTime(),
                TextColumn::make('last_checked_at')->label('آخرین بررسی')->jalaliDateTime(),
                TextColumn::make('tls_status')->label('TLS')->badge(),
                TextColumn::make('tls_expires_at')->label('انقضا TLS')->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        SiteDomain::STATUS_PENDING => 'در انتظار',
                        SiteDomain::STATUS_VERIFIED => 'تایید شده',
                        SiteDomain::STATUS_FAILED => 'ناموفق',
                    ]),
            ])
            ->actions([
                Action::make('request_verification')
                    ->label('درخواست تایید')
                    ->requiresConfirmation()
                    ->action(function (SiteDomain $record): void {
                        app(SiteDomainService::class)->requestVerification($record);
                    }),
                Action::make('verify')
                    ->label('بررسی DNS')
                    ->requiresConfirmation()
                    ->action(function (SiteDomain $record): void {
                        app(SiteDomainService::class)->verify($record);
                    }),
                Action::make('request_tls')
                    ->label('درخواست TLS')
                    ->requiresConfirmation()
                    ->visible(fn (SiteDomain $record): bool => (bool) $record->verified_at)
                    ->action(function (SiteDomain $record): void {
                        app(SiteDomainService::class)->requestTls($record);
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteDomains::route('/'),
            'create' => CreateSiteDomain::route('/create'),
            'edit' => EditSiteDomain::route('/{record}/edit'),
        ];
    }
}
