<?php

namespace Vendor\FilamentAccountingIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource\Pages\CreateInventoryDoc;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource\Pages\EditInventoryDoc;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource\Pages\ListInventoryDocs;
use Vendor\FilamentAccountingIr\Filament\Resources\InventoryDocResource\RelationManagers\InventoryDocLinesRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;
use Vendor\FilamentAccountingIr\Services\InventoryDocService;

class InventoryDocResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = InventoryDoc::class;

    protected static ?string $modelLabel = 'سند انبار';

    protected static ?string $pluralModelLabel = 'اسناد انبار';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationLabel = 'اسناد انبار';

    protected static string|\UnitEnum|null $navigationGroup = 'انبار';

    protected static ?int $navigationSort = 3;

    protected static array $eagerLoad = ['company', 'warehouse'];

    public static function canEdit($record): bool
    {
        return $record instanceof InventoryDoc
            && $record->status !== 'posted'
            && auth()->user()?->can('update', $record);
    }

    public static function canDelete($record): bool
    {
        return $record instanceof InventoryDoc
            && $record->status !== 'posted'
            && auth()->user()?->can('delete', $record);
    }

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
                Select::make('warehouse_id')
                    ->label('انبار')
                    ->options(fn () => InventoryWarehouse::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('doc_type')
                    ->label('نوع سند')
                    ->options([
                        'receipt' => 'رسید',
                        'issue' => 'حواله',
                        'transfer' => 'انتقال',
                        'adjustment' => 'اصلاحیه',
                    ])
                    ->default('receipt'),
                TextInput::make('doc_no')
                    ->label('شماره سند')
                    ->maxLength(64),
                DatePicker::make('doc_date')
                    ->label('تاریخ')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'posted' => 'قطعی',
                    ])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('description')
                    ->label('شرح')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doc_no')->label('شماره')->searchable(),
                TextColumn::make('doc_type')->label('نوع')->badge(),
                TextColumn::make('doc_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('warehouse.name')->label('انبار')->sortable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
            ])
            ->actions([
                TableAction::make('post')
                    ->label('قطعی کردن')
                    ->visible(fn (InventoryDoc $record) => $record->status !== 'posted' && auth()->user()?->can('post', $record))
                    ->requiresConfirmation()
                    ->action(function (InventoryDoc $record): void {
                        app(InventoryDocService::class)->post($record);
                        Notification::make()->title('سند انبار قطعی شد.')->success()->send();
                    }),
            ])
            ->defaultSort('doc_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            InventoryDocLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryDocs::route('/'),
            'create' => CreateInventoryDoc::route('/create'),
            'edit' => EditInventoryDoc::route('/{record}/edit'),
        ];
    }
}
