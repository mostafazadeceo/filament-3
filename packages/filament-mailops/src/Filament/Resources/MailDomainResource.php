<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages\CreateMailDomain;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages\EditMailDomain;
use Haida\FilamentMailOps\Filament\Resources\MailDomainResource\Pages\ListMailDomains;
use Haida\FilamentMailOps\Models\MailDomain;
use Haida\FilamentMailOps\Services\MailuSyncService;
use Haida\FilamentMailOps\Support\MailOpsLabels;
use Filamat\IamSuite\Support\TenantContext;
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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')
                    ->label('نام دامنه')
                    ->required()
                    ->maxLength(255)
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
                    ->maxLength(255)
                    ->default('dkim'),
                Textarea::make('dkim_public_key')
                    ->label('کلید عمومی DKIM')
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('comment')
                    ->label('یادداشت')
                    ->rows(3)
                    ->columnSpanFull(),
                KeyValue::make('dns_snapshot')
                    ->label('DNS Snapshot')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('دامنه')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => MailOpsLabels::status($state)),
                TextColumn::make('sync_status')
                    ->label('همگام‌سازی')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => MailOpsLabels::syncStatus($state)),
                TextColumn::make('mailu_synced_at')
                    ->label('آخرین همگام‌سازی')
                    ->jalaliDateTime(),
                TextColumn::make('updated_at')
                    ->label('آخرین تغییر')
                    ->jalaliDateTime(),
            ])
            ->actions([
                Action::make('sync_mailu')
                    ->label('همگام‌سازی Mailu')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (MailDomain $record) => config('filament-mailops.mailu.enabled')
                        && IamAuthorization::allows('mailops.domain.manage', IamAuthorization::resolveTenantFromRecord($record)))
                    ->action(function (MailDomain $record, MailuSyncService $service): void {
                        $service->syncDomain($record);
                        Notification::make()->title('دامنه همگام شد.')->success()->send();
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
}
