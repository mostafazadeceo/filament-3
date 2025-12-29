<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollRunResource\Pages\CreatePayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollRunResource\Pages\EditPayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollRunResource\Pages\ListPayrollRuns;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollRunResource\RelationManagers\PayrollSlipsRelationManager;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Services\PayrollRunService;

class PayrollRunResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.run';

    protected static ?string $model = PayrollRun::class;

    protected static ?string $modelLabel = 'دوره حقوق';

    protected static ?string $pluralModelLabel = 'دوره‌های حقوق';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document';

    protected static string|\UnitEnum|null $navigationGroup = 'حقوق و دستمزد';

    public static function canEdit($record): bool
    {
        return $record instanceof PayrollRun
            && ! in_array($record->status, ['posted', 'locked'], true)
            && auth()->user()?->can('update', $record);
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
                DatePicker::make('period_start')
                    ->label('شروع دوره')
                    ->required(),
                DatePicker::make('period_end')
                    ->label('پایان دوره')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'draft' => 'پیش‌نویس',
                        'approved' => 'تایید شده',
                        'posted' => 'قطعی',
                        'locked' => 'قفل شده',
                    ])
                    ->default('draft')
                    ->disabled()
                    ->dehydrated(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('period_start')->label('شروع')->jalaliDate()->sortable(),
                TextColumn::make('period_end')->label('پایان')->jalaliDate()->sortable(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('approved_at')->label('تایید')->jalaliDateTime(),
                TextColumn::make('posted_at')->label('قطعی')->jalaliDateTime(),
            ])
            ->actions([
                TableAction::make('generate')
                    ->label('محاسبه حقوق')
                    ->visible(fn (PayrollRun $record) => $record->status === 'draft' && auth()->user()?->can('update', $record))
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record): void {
                        app(PayrollRunService::class)->generate($record);
                        Notification::make()->title('محاسبات حقوق انجام شد.')->success()->send();
                    }),
                TableAction::make('approve')
                    ->label('تایید')
                    ->visible(fn (PayrollRun $record) => $record->status === 'draft' && auth()->user()?->can('approve', $record))
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                        Notification::make()->title('دوره تایید شد.')->success()->send();
                    }),
                TableAction::make('post')
                    ->label('ثبت قطعی')
                    ->visible(fn (PayrollRun $record) => $record->status === 'approved' && auth()->user()?->can('post', $record))
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record): void {
                        $record->update([
                            'status' => 'posted',
                            'posted_at' => now(),
                        ]);
                        Notification::make()->title('دوره قطعی شد.')->success()->send();
                    }),
                TableAction::make('lock')
                    ->label('قفل')
                    ->visible(fn (PayrollRun $record) => $record->status === 'posted' && auth()->user()?->can('lock', $record))
                    ->requiresConfirmation()
                    ->action(function (PayrollRun $record): void {
                        $record->update([
                            'status' => 'locked',
                            'locked_at' => now(),
                        ]);
                        Notification::make()->title('دوره قفل شد.')->success()->send();
                    }),
            ])
            ->defaultSort('period_start', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            PayrollSlipsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollRuns::route('/'),
            'create' => CreatePayrollRun::route('/create'),
            'edit' => EditPayrollRun::route('/{record}/edit'),
        ];
    }
}
