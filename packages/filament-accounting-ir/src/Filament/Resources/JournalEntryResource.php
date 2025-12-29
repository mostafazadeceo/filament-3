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
use Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource\Pages\CreateJournalEntry;
use Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource\Pages\EditJournalEntry;
use Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource\Pages\ListJournalEntries;
use Vendor\FilamentAccountingIr\Filament\Resources\JournalEntryResource\RelationManagers\JournalLinesRelationManager;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Models\JournalEntry;
use Vendor\FilamentAccountingIr\Services\JournalEntryService;

class JournalEntryResource extends Resource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $model = JournalEntry::class;

    protected static ?string $modelLabel = 'سند حسابداری';

    protected static ?string $pluralModelLabel = 'اسناد حسابداری';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'اسناد حسابداری';

    protected static string|\UnitEnum|null $navigationGroup = 'هسته حسابداری';

    protected static ?int $navigationSort = 5;

    protected static array $eagerLoad = ['company'];

    public static function canEdit($record): bool
    {
        return $record instanceof JournalEntry
            && $record->status !== 'posted'
            && auth()->user()?->can('update', $record);
    }

    public static function canDelete($record): bool
    {
        return false;
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
                Select::make('branch_id')
                    ->label('شعبه')
                    ->options(fn () => AccountingBranch::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                Select::make('fiscal_year_id')
                    ->label('سال مالی')
                    ->options(fn () => FiscalYear::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                Select::make('fiscal_period_id')
                    ->label('دوره مالی')
                    ->options(fn () => FiscalPeriod::query()->pluck('name', 'id')->toArray())
                    ->searchable(),
                TextInput::make('entry_no')
                    ->label('شماره سند')
                    ->required()
                    ->maxLength(64),
                DatePicker::make('entry_date')
                    ->label('تاریخ سند')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'submitted' => 'ارسال شده',
                        'approved' => 'تایید شده',
                        'posted' => 'قطعی',
                    ])
                    ->default('draft'),
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
                TextColumn::make('entry_no')->label('شماره')->searchable()->sortable(),
                TextColumn::make('entry_date')->label('تاریخ')->jalaliDate()->sortable(),
                TextColumn::make('company.name')->label('شرکت')->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('total_debit')->label('بدهکار')->numeric(decimalPlaces: 0),
                TextColumn::make('total_credit')->label('بستانکار')->numeric(decimalPlaces: 0),
            ])
            ->actions([
                TableAction::make('submit')
                    ->label('ارسال')
                    ->visible(fn (JournalEntry $record) => $record->status === 'draft' && auth()->user()?->can('submit', $record))
                    ->action(function (JournalEntry $record): void {
                        app(JournalEntryService::class)->submit($record);
                        Notification::make()->title('سند ارسال شد.')->success()->send();
                    }),
                TableAction::make('approve')
                    ->label('تایید')
                    ->visible(fn (JournalEntry $record) => $record->status === 'submitted' && auth()->user()?->can('approve', $record))
                    ->action(function (JournalEntry $record): void {
                        app(JournalEntryService::class)->approve($record);
                        Notification::make()->title('سند تایید شد.')->success()->send();
                    }),
                TableAction::make('post')
                    ->label('قطعی')
                    ->visible(fn (JournalEntry $record) => $record->status === 'approved' && auth()->user()?->can('post', $record))
                    ->action(function (JournalEntry $record): void {
                        app(JournalEntryService::class)->post($record);
                        Notification::make()->title('سند قطعی شد.')->success()->send();
                    }),
            ])
            ->defaultSort('entry_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            JournalLinesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListJournalEntries::route('/'),
            'create' => CreateJournalEntry::route('/create'),
            'edit' => EditJournalEntry::route('/{record}/edit'),
        ];
    }
}
