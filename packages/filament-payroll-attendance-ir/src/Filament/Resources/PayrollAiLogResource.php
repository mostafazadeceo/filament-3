<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAiLogResource\Pages\ListPayrollAiLogs;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAiLogResource\Pages\ViewPayrollAiLog;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAiLog;

class PayrollAiLogResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.ai';

    protected static ?string $model = PayrollAiLog::class;

    protected static ?string $modelLabel = 'لاگ هوش مصنوعی';

    protected static ?string $pluralModelLabel = 'لاگ‌های هوش مصنوعی';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'هوش مصنوعی';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')->label('زمان')->jalaliDateTime()->sortable(),
                TextColumn::make('report_type')->label('نوع گزارش')->badge(),
                TextColumn::make('company_id')->label('شرکت')->toggleable(),
                TextColumn::make('actor_id')->label('کاربر')->toggleable(),
                TextColumn::make('provider')->label('Provider')->toggleable(),
                TextColumn::make('response_summary')->label('خلاصه')->limit(80),
            ])
            ->filters([
                SelectFilter::make('report_type')
                    ->label('نوع گزارش')
                    ->options(fn () => PayrollAiLog::query()
                        ->whereNotNull('report_type')
                        ->distinct()
                        ->pluck('report_type')
                        ->mapWithKeys(fn ($value) => [$value => $value])
                        ->toArray())
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make()->label('مشاهده'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        $showPayloads = (bool) config('filament-payroll-attendance-ir.ai.log_payloads', false);

        return $schema
            ->components([
                Section::make('جزئیات گزارش')
                    ->schema([
                        TextEntry::make('report_type')->label('نوع گزارش'),
                        TextEntry::make('company_id')->label('شرکت'),
                        TextEntry::make('actor_id')->label('کاربر'),
                        TextEntry::make('period_start')->label('شروع بازه')->jalaliDate(),
                        TextEntry::make('period_end')->label('پایان بازه')->jalaliDate(),
                        TextEntry::make('provider')->label('Provider'),
                        TextEntry::make('input_hash')->label('هش ورودی'),
                        TextEntry::make('response_summary')->label('خلاصه')->columnSpanFull(),
                        TextEntry::make('created_at')->label('زمان')->jalaliDateTime(),
                    ])
                    ->columns(2),
                Section::make('Payload')
                    ->visible($showPayloads)
                    ->schema([
                        TextEntry::make('input_payload')
                            ->label('ورودی')
                            ->formatStateUsing(fn ($state) => $state
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                : null)
                            ->columnSpanFull(),
                        TextEntry::make('output_payload')
                            ->label('خروجی')
                            ->formatStateUsing(fn ($state) => $state
                                ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                                : null)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollAiLogs::route('/'),
            'view' => ViewPayrollAiLog::route('/{record}'),
        ];
    }
}
