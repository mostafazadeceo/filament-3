<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages\CreateMailDomain;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages\EditMailDomain;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages\ListMailDomains;
use Haida\FilamentMailOps\Models\MailDomain;
use Haida\FilamentMailOps\Services\DomainDnsAuditService;
use Haida\FilamentMailOps\Services\MailuSyncService;
use Haida\FilamentMailOps\Support\MailOpsLabels;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique;

class MailDomainResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailDomain::class;

    protected static ?string $modelLabel = 'دامنه ایمیل';

    protected static ?string $pluralModelLabel = 'دامنه‌های ایمیل';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'ایمیل';

    protected static ?string $permissionPrefix = 'mailops.domain';

    private const DOMAIN_REGEX = '/^(?=.{1,253}$)(?!-)(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)(?:\\.(?!-)[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?)+$/i';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Section::make('دامنه و وضعیت')
                    ->schema([
                        TextInput::make('name')
                            ->label('نام دامنه')
                            ->required()
                            ->maxLength(253)
                            ->rule('regex:'.self::DOMAIN_REGEX)
                            ->helperText('نمونه: abrak.org')
                            ->unique(
                                table: config('filament-mailops.tables.domains', 'mailops_domains'),
                                column: 'name',
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->where('tenant_id', (int) TenantContext::getTenantId()),
                            ),
                        Select::make('status')
                            ->label('وضعیت')
                            ->options([
                                'active' => 'فعال',
                                'inactive' => 'غیرفعال',
                                'pending' => 'در انتظار',
                                'failed' => 'ناموفق',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('dkim_selector')
                            ->label('DKIM Selector')
                            ->maxLength(63)
                            ->default('dkim')
                            ->helperText('فقط حروف/عدد/.-_'),
                        Textarea::make('comment')
                            ->label('یادداشت')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('امنیت ایمیل و DNS')
                    ->description('رکوردهای MX/SPF/DKIM/DMARC برای تحویل صحیح ایمیل حیاتی هستند.')
                    ->schema([
                        Textarea::make('dkim_public_key')
                            ->label('کلید عمومی DKIM')
                            ->rows(6)
                            ->columnSpanFull(),
                        Placeholder::make('dns_health_preview')
                            ->label('سلامت DNS')
                            ->content(function (?MailDomain $record): string {
                                if (! $record) {
                                    return 'پس از ذخیره، بررسی DNS در این بخش نمایش داده می‌شود.';
                                }

                                $score = $record->dns_health_score;
                                $status = static::dnsHealthLabel($record->dns_health_status);

                                return is_numeric($score) ? "{$status} ({$score}%)" : $status;
                            }),
                        Placeholder::make('dns_issues_preview')
                            ->label('نکات قابل اصلاح DNS')
                            ->content(function (?MailDomain $record): string {
                                if (! $record || ! is_array($record->dns_issues) || $record->dns_issues === []) {
                                    return 'خطای بحرانی ثبت نشده است.';
                                }

                                return implode(PHP_EOL, array_slice($record->dns_issues, 0, 6));
                            }),
                        KeyValue::make('dns_snapshot')
                            ->label('DNS Snapshot (Mailu)')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('وضعیت همگام‌سازی')
                    ->schema([
                        Placeholder::make('sync_status_view')
                            ->label('وضعیت Sync')
                            ->content(fn (?MailDomain $record): string => $record ? MailOpsLabels::syncStatus($record->sync_status) : '-'),
                        Placeholder::make('mailu_synced_at_view')
                            ->label('آخرین همگام‌سازی')
                            ->content(fn (?MailDomain $record): string => $record?->mailu_synced_at?->toDateTimeString() ?? '-'),
                        Placeholder::make('last_error_view')
                            ->label('آخرین خطا')
                            ->content(fn (?MailDomain $record): string => $record?->last_error ?: '-')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount(['mailboxes', 'aliases']))
            ->columns([
                TextColumn::make('name')
                    ->label('دامنه')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => MailOpsLabels::status($state)),
                TextColumn::make('sync_status')
                    ->label('همگام‌سازی')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => MailOpsLabels::syncStatus($state)),
                TextColumn::make('dns_health_status')
                    ->label('سلامت DNS')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => static::dnsHealthLabel($state)),
                TextColumn::make('dns_health_score')
                    ->label('امتیاز DNS')
                    ->formatStateUsing(fn ($state): string => is_numeric($state) ? "{$state}%" : '-')
                    ->sortable(),
                TextColumn::make('mailboxes_count')
                    ->label('تعداد صندوق')
                    ->sortable(),
                TextColumn::make('aliases_count')
                    ->label('تعداد Alias')
                    ->sortable(),
                TextColumn::make('mailu_synced_at')
                    ->label('آخرین همگام‌سازی')
                    ->jalaliDateTime(),
                TextColumn::make('updated_at')
                    ->label('آخرین تغییر')
                    ->jalaliDateTime(),
                TextColumn::make('last_error')
                    ->label('آخرین خطا')
                    ->limit(50)
                    ->tooltip(fn (?string $state): string => $state ?: '-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                        'pending' => 'در انتظار',
                        'failed' => 'ناموفق',
                    ]),
                SelectFilter::make('sync_status')
                    ->label('Sync')
                    ->options([
                        'pending' => 'در انتظار',
                        'synced' => 'همگام',
                        'failed' => 'ناموفق',
                    ]),
                SelectFilter::make('dns_health_status')
                    ->label('سلامت DNS')
                    ->options([
                        'healthy' => 'سالم',
                        'warning' => 'نیازمند بهبود',
                        'critical' => 'بحرانی',
                        'unknown' => 'نامشخص',
                    ]),
            ])
            ->actions([
                Action::make('sync_mailu')
                    ->label('همگام‌سازی Mailu')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (MailDomain $record) => config('filament-mailops.mailu.enabled')
                        && IamAuthorization::allows('mailops.domain.manage', IamAuthorization::resolveTenantFromRecord($record)))
                    ->action(function (MailDomain $record, MailuSyncService $service): void {
                        $domain = $service->syncDomain($record);

                        if ($domain->sync_status === 'failed') {
                            Notification::make()
                                ->title('همگام‌سازی دامنه ناموفق بود.')
                                ->body($domain->last_error ?: 'خطای نامشخص')
                                ->danger()
                                ->send();

                            return;
                        }

                        $score = is_numeric($domain->dns_health_score) ? "{$domain->dns_health_score}%" : '-';
                        Notification::make()
                            ->title('دامنه همگام شد.')
                            ->body("امتیاز سلامت DNS: {$score}")
                            ->success()
                            ->send();
                    }),
                Action::make('refresh_dns')
                    ->label('به‌روزرسانی Snapshot DNS')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (MailDomain $record) => config('filament-mailops.mailu.enabled')
                        && IamAuthorization::allows('mailops.domain.manage', IamAuthorization::resolveTenantFromRecord($record)))
                    ->action(function (MailDomain $record, MailuSyncService $service): void {
                        $domain = $service->refreshDomainDnsSnapshot($record);

                        if ($domain->sync_status === 'failed') {
                            Notification::make()
                                ->title('به‌روزرسانی Snapshot ناموفق بود.')
                                ->body($domain->last_error ?: 'خطای نامشخص')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Snapshot DNS به‌روزرسانی شد.')
                            ->body(static::dnsSummary($domain))
                            ->success()
                            ->send();
                    }),
                Action::make('audit_dns')
                    ->label('ممیزی DNS')
                    ->icon('heroicon-o-shield-check')
                    ->visible(fn (MailDomain $record) => IamAuthorization::allows('mailops.domain.manage', IamAuthorization::resolveTenantFromRecord($record)))
                    ->action(function (MailDomain $record, DomainDnsAuditService $auditService): void {
                        $domain = $auditService->applyToDomain($record->refresh());
                        $notification = Notification::make()
                            ->title('ممیزی DNS انجام شد.')
                            ->body(static::dnsSummary($domain));

                        if ($domain->dns_health_status === 'healthy') {
                            $notification->success()->send();

                            return;
                        }

                        $notification->warning()->send();
                    }),
                Action::make('dns_records')
                    ->label('راهنمای رکورد DNS')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->visible(fn (MailDomain $record) => IamAuthorization::allows('mailops.domain.view', IamAuthorization::resolveTenantFromRecord($record)))
                    ->fillForm(fn (MailDomain $record, DomainDnsAuditService $auditService): array => [
                        'records' => $auditService->recordsAsText($record->dns_snapshot),
                    ])
                    ->form([
                        Textarea::make('records')
                            ->label('رکوردها (Copy/Paste)')
                            ->rows(14)
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->action(static function (): void {}),
                Action::make('copy_dns_issues')
                    ->label('نمایش خطاهای DNS')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->visible(fn (MailDomain $record) => is_array($record->dns_issues) && $record->dns_issues !== [])
                    ->action(function (MailDomain $record): void {
                        Notification::make()
                            ->title('خطاهای DNS')
                            ->body(static::dnsIssues($record))
                            ->warning()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailDomains::route('/'),
            'create' => CreateMailDomain::route('/create'),
            'edit' => EditMailDomain::route('/{record}/edit'),
        ];
    }

    protected static function dnsHealthLabel(?string $status): string
    {
        return app(DomainDnsAuditService::class)->healthLabel($status);
    }

    protected static function dnsSummary(MailDomain $domain): string
    {
        $score = is_numeric($domain->dns_health_score) ? "{$domain->dns_health_score}%" : '-';
        $status = static::dnsHealthLabel($domain->dns_health_status);

        return "وضعیت: {$status} | امتیاز: {$score}";
    }

    protected static function dnsIssues(MailDomain $domain): string
    {
        if (! is_array($domain->dns_issues) || $domain->dns_issues === []) {
            return 'خطای ثبت‌شده‌ای وجود ندارد.';
        }

        return implode(PHP_EOL, array_slice($domain->dns_issues, 0, 8));
    }
}
