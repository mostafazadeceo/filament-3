<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailOps\Filament\Resources\MailMailboxResource\Pages\CreateMailMailbox;
use Haida\FilamentMailOps\Filament\Resources\MailMailboxResource\Pages\EditMailMailbox;
use Haida\FilamentMailOps\Filament\Resources\MailMailboxResource\Pages\ListMailMailboxes;
use Haida\FilamentMailOps\Models\MailDomain;
use Haida\FilamentMailOps\Models\MailMailbox;
use Haida\FilamentMailOps\Services\ImapInboxReader;
use Haida\FilamentMailOps\Services\MailuSyncService;
use Haida\FilamentMailOps\Support\MailOpsLabels;
use Illuminate\Database\Eloquent\Builder;

class MailMailboxResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailMailbox::class;

    protected static ?string $modelLabel = 'صندوق ایمیل';

    protected static ?string $pluralModelLabel = 'صندوق‌های ایمیل';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    protected static string|\UnitEnum|null $navigationGroup = 'ایمیل';

    protected static ?string $permissionPrefix = 'mailops.mailbox';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('domain_id')
                    ->label('دامنه')
                    ->options(fn () => static::scopeByTenant(MailDomain::query())
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (Set $set, Get $get) => $set('email_preview', self::buildEmailPreview($get))),
                TextInput::make('local_part')
                    ->label('نام کاربری')
                    ->required()
                    ->maxLength(255)
                    ->helperText('بخش قبل از @')
                    ->reactive()
                    ->afterStateUpdated(fn (Set $set, Get $get) => $set('email_preview', self::buildEmailPreview($get))),
                TextInput::make('email_preview')
                    ->label('آدرس کامل')
                    ->disabled()
                    ->dehydrated(false)
                    ->afterStateHydrated(fn (Set $set, Get $get) => $set('email_preview', self::buildEmailPreview($get)))
                    ->columnSpanFull(),
                TextInput::make('password')
                    ->label('رمز عبور')
                    ->password()
                    ->revealable()
                    ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                    ->dehydrated(fn ($state) => filled($state)),
                TextInput::make('display_name')
                    ->label('نام نمایشی')
                    ->maxLength(255),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                TextInput::make('quota_bytes')
                    ->label('سقف فضا (بایت)')
                    ->numeric()
                    ->helperText('اختیاری؛ برحسب بایت.'),
                Section::make('تنظیمات پیشرفته')
                    ->schema([
                        Toggle::make('settings.enable_imap')
                            ->label('فعال‌سازی IMAP')
                            ->default(true),
                        Toggle::make('settings.enable_pop')
                            ->label('فعال‌سازی POP3')
                            ->default(false),
                        Toggle::make('settings.allow_spoofing')
                            ->label('اجازه جعل فرستنده')
                            ->default(false),
                        Toggle::make('settings.forward_enabled')
                            ->label('فعال‌سازی فوروارد')
                            ->default(false)
                            ->reactive(),
                        TagsInput::make('settings.forward_destination')
                            ->label('فوروارد به')
                            ->visible(fn (Get $get) => (bool) $get('settings.forward_enabled')),
                        Toggle::make('settings.forward_keep')
                            ->label('نگهداشت کپی در اینباکس')
                            ->default(true)
                            ->visible(fn (Get $get) => (bool) $get('settings.forward_enabled')),
                        Toggle::make('settings.reply_enabled')
                            ->label('پاسخ‌گوی خودکار')
                            ->default(false)
                            ->reactive(),
                        TextInput::make('settings.reply_subject')
                            ->label('عنوان پاسخ خودکار')
                            ->visible(fn (Get $get) => (bool) $get('settings.reply_enabled')),
                        Textarea::make('settings.reply_body')
                            ->label('متن پاسخ خودکار')
                            ->rows(4)
                            ->visible(fn (Get $get) => (bool) $get('settings.reply_enabled')),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Section::make('اتصال SMTP/IMAP')
                    ->schema([
                        TextInput::make('settings.smtp_host')
                            ->label('SMTP Host')
                            ->helperText('اگر خالی باشد از تنظیمات سراسری استفاده می‌شود.')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),
                        TextInput::make('settings.smtp_port')
                            ->label('SMTP Port')
                            ->numeric()
                            ->helperText('اگر خالی باشد از تنظیمات سراسری استفاده می‌شود.')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null),
                        Select::make('settings.smtp_encryption')
                            ->label('SMTP Encryption')
                            ->options([
                                'tls' => 'TLS',
                                'ssl' => 'SSL',
                                'none' => 'بدون رمزنگاری',
                            ])
                            ->helperText('اگر خالی باشد از تنظیمات سراسری استفاده می‌شود.')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),
                        TextInput::make('settings.imap_host')
                            ->label('IMAP Host')
                            ->helperText('اگر خالی باشد از تنظیمات سراسری استفاده می‌شود.')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),
                        TextInput::make('settings.imap_port')
                            ->label('IMAP Port')
                            ->numeric()
                            ->helperText('اگر خالی باشد از تنظیمات سراسری استفاده می‌شود.')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null),
                        Select::make('settings.imap_encryption')
                            ->label('IMAP Encryption')
                            ->options([
                                'ssl' => 'SSL',
                                'tls' => 'TLS',
                                'none' => 'بدون رمزنگاری',
                            ])
                            ->helperText('اگر خالی باشد از تنظیمات سراسری استفاده می‌شود.')
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null),
                        Toggle::make('settings.imap_verify_tls')
                            ->label('اعتبارسنجی TLS برای IMAP')
                            ->default(true),
                    ])
                    ->columns(2)
                    ->collapsed(),
                Textarea::make('comment')
                    ->label('یادداشت')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('domain'))
            ->columns([
                TextColumn::make('email')
                    ->label('ایمیل')
                    ->searchable(),
                TextColumn::make('domain.name')
                    ->label('دامنه')
                    ->sortable(),
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
                    ->visible(fn (MailMailbox $record) => config('filament-mailops.mailu.enabled')
                        && IamAuthorization::allows('mailops.mailbox.manage', IamAuthorization::resolveTenantFromRecord($record)))
                    ->action(function (MailMailbox $record, MailuSyncService $service): void {
                        $service->syncMailbox($record, $record->password);
                        Notification::make()->title('صندوق همگام شد.')->success()->send();
                    }),
                Action::make('sync_inbox')
                    ->label('همگام‌سازی اینباکس')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->visible(fn (MailMailbox $record) => IamAuthorization::allows('mailops.inbound.sync', IamAuthorization::resolveTenantFromRecord($record)))
                    ->action(function (MailMailbox $record, ImapInboxReader $reader): void {
                        $count = $reader->sync($record);
                        Notification::make()->title("{$count} پیام همگام شد.")->success()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailMailboxes::route('/'),
            'create' => CreateMailMailbox::route('/create'),
            'edit' => EditMailMailbox::route('/{record}/edit'),
        ];
    }

    protected static function buildEmailPreview(Get $get): string
    {
        $domainId = $get('domain_id');
        $local = $get('local_part');
        if (! $domainId || ! $local) {
            return '-';
        }

        $domain = MailDomain::query()->find($domainId);
        if (! $domain) {
            return '-';
        }

        return $local.'@'.$domain->name;
    }
}
