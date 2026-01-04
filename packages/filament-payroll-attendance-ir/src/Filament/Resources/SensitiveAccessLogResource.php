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
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\SensitiveAccessLog;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\SensitiveAccessLogResource\Pages\ListSensitiveAccessLogs;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\SensitiveAccessLogResource\Pages\ViewSensitiveAccessLog;

class SensitiveAccessLogResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.audit';

    protected static ?string $model = SensitiveAccessLog::class;

    protected static ?string $modelLabel = 'ثبت دسترسی حساس';

    protected static ?string $pluralModelLabel = 'ثبت‌های دسترسی حساس';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'ممیزی';

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
                TextColumn::make('actor_id')->label('کاربر'),
                TextColumn::make('subject_type')
                    ->label('نوع')
                    ->formatStateUsing(fn ($state) => $state ? class_basename((string) $state) : null),
                TextColumn::make('subject_id')->label('شناسه'),
                TextColumn::make('reason')->label('علت'),
                TextColumn::make('company_id')->label('شرکت')->toggleable(),
                TextColumn::make('branch_id')->label('شعبه')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('subject_type')
                    ->label('نوع')
                    ->options(fn () => SensitiveAccessLog::query()
                        ->whereNotNull('subject_type')
                        ->distinct()
                        ->pluck('subject_type')
                        ->mapWithKeys(fn ($value) => [$value => class_basename((string) $value)])
                        ->toArray())
                    ->searchable(),
                SelectFilter::make('reason')
                    ->label('علت')
                    ->options(fn () => SensitiveAccessLog::query()
                        ->whereNotNull('reason')
                        ->distinct()
                        ->pluck('reason')
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
        return $schema
            ->components([
                Section::make('جزئیات دسترسی')
                    ->schema([
                        TextEntry::make('actor_id')->label('کاربر'),
                        TextEntry::make('subject_type')
                            ->label('نوع')
                            ->formatStateUsing(fn ($state) => $state ? class_basename((string) $state) : null),
                        TextEntry::make('subject_id')->label('شناسه'),
                        TextEntry::make('reason')->label('علت'),
                        TextEntry::make('company_id')->label('شرکت'),
                        TextEntry::make('branch_id')->label('شعبه'),
                        TextEntry::make('created_at')->label('زمان')->jalaliDateTime(),
                    ])
                    ->columns(2),
                Section::make('متادیتا')
                    ->schema([
                        TextEntry::make('metadata')
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
            'index' => ListSensitiveAccessLogs::route('/'),
            'view' => ViewSensitiveAccessLog::route('/{record}'),
        ];
    }
}
