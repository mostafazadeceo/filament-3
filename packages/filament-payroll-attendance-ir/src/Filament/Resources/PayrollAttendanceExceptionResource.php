<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action as TableAction;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\ResolveException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns\HasEagerLoads;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceExceptionResource\Pages\ListPayrollAttendanceExceptions;

class PayrollAttendanceExceptionResource extends IamResource
{
    use HasEagerLoads;
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'payroll.exception';

    protected static ?string $model = AttendanceException::class;

    protected static ?string $modelLabel = 'استثنا';

    protected static ?string $pluralModelLabel = 'صندوق استثناها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'حضور و غیاب';

    protected static array $eagerLoad = ['employee', 'assignedTo'];

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.first_name')
                    ->label('پرسنل')
                    ->formatStateUsing(fn ($state, AttendanceException $record) => trim($record->employee?->first_name.' '.$record->employee?->last_name))
                    ->searchable(),
                TextColumn::make('type')->label('نوع'),
                TextColumn::make('severity')->label('شدت')->badge(),
                TextColumn::make('status')->label('وضعیت')->badge(),
                TextColumn::make('detected_at')->label('تاریخ')->jalaliDateTime(),
                TextColumn::make('assignedTo.name')->label('مسئول'),
            ])
            ->actions([
                TableAction::make('resolve')
                    ->label('رفع')
                    ->visible(fn (AttendanceException $record) => in_array($record->status?->value ?? (string) $record->status, ['open', 'in_review'], true)
                        && auth()->user()?->can('resolve', $record))
                    ->form([
                        Textarea::make('resolution_notes')
                            ->label('توضیحات رفع')
                            ->required(),
                    ])
                    ->action(function (AttendanceException $record, array $data): void {
                        app(ResolveException::class)->execute($record, [
                            'resolution_notes' => $data['resolution_notes'] ?? null,
                        ]);
                    }),
            ])
            ->defaultSort('detected_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayrollAttendanceExceptions::route('/'),
        ];
    }
}
