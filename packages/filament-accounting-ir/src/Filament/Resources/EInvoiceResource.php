<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\Pages\CreateEInvoice;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\Pages\EditEInvoice;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\Pages\ListEInvoices;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\RelationManagers\EInvoiceLinesRelationManager;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\RelationManagers\EInvoiceStatusLogsRelationManager;
use Vendor\FilamentAccountingIr\Filament\Resources\EInvoiceResource\RelationManagers\EInvoiceSubmissionsRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\EInvoice;
use Vendor\FilamentAccountingIr\Models\EInvoiceProvider;
use Vendor\FilamentAccountingIr\Models\SalesInvoice;
use Vendor\FilamentAccountingIr\Services\EInvoice\EInvoiceService;

class EInvoiceResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = EInvoice::class;

    protected static ?string $modelLabel = 'صورتحساب الکترونیکی';

    protected static ?string $pluralModelLabel = 'صورتحساب‌های الکترونیکی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'صورتحساب‌های الکترونیکی';

    protected static string|\UnitEnum|null $navigationGroup = 'سامانه مؤدیان';

    protected static ?int $navigationSort = 2;

    protected static array $eagerLoad = ['company', 'provider', 'salesInvoice'];

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
                Select::make('sales_invoice_id')
                    ->label('فاکتور فروش')
                    ->options(fn () => SalesInvoice::query()->pluck('invoice_no', 'id')->toArray())
                    ->searchable(),
                Select::make('provider_id')
                    ->label('ارائه‌دهنده')
                    ->options(fn () => EInvoiceProvider::query()->where('is_active', true)->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('invoice_type')
                    ->label('نوع')
                    ->default('standard')
                    ->maxLength(64),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'queued' => 'در صف',
                        'sent' => 'ارسال شده',
                        'failed' => 'ناموفق',
                        'cancelled' => 'باطل شده',
                    ])
                    ->default('draft'),
                TextInput::make('unique_tax_id')
                    ->label('شناسه یکتا')
                    ->maxLength(255),
                TextInput::make('payload_version')
                    ->label('نسخه')
                    ->default('v1')
                    ->maxLength(32),
                DateTimePicker::make('issued_at')
                    ->label('زمان صدور'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unique_tax_id')->label('شناسه یکتا')->searchable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('salesInvoice.invoice_no')->label('فاکتور')->sortable(),
                TextColumn::make('provider.name')->label('ارائه‌دهنده')->sortable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                TextColumn::make('issued_at')->label('صدور')->jalaliDateTime(),
            ])
            ->actions([
                TableAction::make('queue_send')
                    ->label('ارسال')
                    ->visible(fn (EInvoice $record) => in_array($record->status, ['draft', 'failed'], true))
                    ->action(function (EInvoice $record): void {
                        app(EInvoiceService::class)->queue($record);
                        Notification::make()->title('ارسال در صف قرار گرفت.')->success()->send();
                    }),
            ])
            ->defaultSort('issued_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            EInvoiceLinesRelationManager::class,
            EInvoiceSubmissionsRelationManager::class,
            EInvoiceStatusLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEInvoices::route('/'),
            'create' => CreateEInvoice::route('/create'),
            'edit' => EditEInvoice::route('/{record}/edit'),
        ];
    }
}
