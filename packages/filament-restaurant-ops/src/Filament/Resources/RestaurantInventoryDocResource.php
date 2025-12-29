<?php

namespace Haida\FilamentRestaurantOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentRestaurantOps\Filament\Resources\Concerns\HasEagerLoads;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantInventoryDocResource\Pages\CreateRestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantInventoryDocResource\Pages\EditRestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantInventoryDocResource\Pages\ListRestaurantInventoryDocs;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantInventoryDocResource\RelationManagers\InventoryDocLinesRelationManager;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
use Haida\FilamentRestaurantOps\Services\RestaurantInventoryDocService;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;

class RestaurantInventoryDocResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'restaurant.inventory_doc';

    protected static ?string $model = RestaurantInventoryDoc::class;

    protected static ?string $modelLabel = 'سند انبار';

    protected static ?string $pluralModelLabel = 'اسناد انبار';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|\UnitEnum|null $navigationGroup = 'انبار';

    protected static array $eagerLoad = ['warehouse'];

    public static function canEdit($record): bool
    {
        return $record instanceof RestaurantInventoryDoc
            && $record->status !== 'posted'
            && auth()->user()?->can('update', $record);
    }

    public static function canDelete($record): bool
    {
        return $record instanceof RestaurantInventoryDoc
            && $record->status !== 'posted'
            && auth()->user()?->can('delete', $record);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('company_id')
                    ->label('شرکت')
                    ->options(fn () => AccountingCompany::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->nullable(),
                Select::make('warehouse_id')
                    ->label('انبار')
                    ->options(fn () => RestaurantWarehouse::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('doc_no')
                    ->label('شماره سند')
                    ->maxLength(64),
                Select::make('doc_type')
                    ->label('نوع سند')
                    ->options([
                        'receipt' => 'رسید',
                        'issue' => 'حواله مصرف',
                        'transfer_out' => 'انتقال خروج',
                        'transfer_in' => 'انتقال ورود',
                        'waste' => 'ضایعات',
                        'adjustment_out' => 'تعدیل کاهشی',
                        'adjustment_in' => 'تعدیل افزایشی',
                        'consumption' => 'مصرف بر اساس فروش',
                    ])
                    ->default('receipt'),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'posted' => 'ثبت شده',
                    ])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),
                DatePicker::make('doc_date')
                    ->label('تاریخ')
                    ->nullable(),
                Textarea::make('notes')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('doc_no')->label('شماره')->searchable()->sortable(),
                TextColumn::make('doc_type')->label('نوع')->badge(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('doc_date')->label('تاریخ')->jalaliDate(),
                TextColumn::make('warehouse.name')->label('انبار'),
            ])
            ->actions([
                TableAction::make('post')
                    ->label('قطعی کردن')
                    ->visible(fn (RestaurantInventoryDoc $record) => $record->status !== 'posted' && auth()->user()?->can('post', $record))
                    ->requiresConfirmation()
                    ->action(function (RestaurantInventoryDoc $record): void {
                        app(RestaurantInventoryDocService::class)->post($record);
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
            'index' => ListRestaurantInventoryDocs::route('/'),
            'create' => CreateRestaurantInventoryDoc::route('/create'),
            'edit' => EditRestaurantInventoryDoc::route('/{record}/edit'),
        ];
    }
}
