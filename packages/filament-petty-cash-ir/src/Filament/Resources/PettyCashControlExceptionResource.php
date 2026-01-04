<?php

namespace Haida\FilamentPettyCashIr\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashControlExceptionResource\Pages\EditPettyCashControlException;
use Haida\FilamentPettyCashIr\Filament\Resources\PettyCashControlExceptionResource\Pages\ListPettyCashControlExceptions;
use Haida\FilamentPettyCashIr\Models\PettyCashControlException;
use Illuminate\Database\Eloquent\Builder;

class PettyCashControlExceptionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'petty_cash.exceptions';

    protected static ?string $model = PettyCashControlException::class;

    protected static ?string $modelLabel = 'استثنا';

    protected static ?string $pluralModelLabel = 'استثناها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'تنخواه';

    protected static array $eagerLoad = ['fund'];

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'open' => 'باز',
                        'triaged' => 'در حال بررسی',
                        'resolved' => 'حل‌شده',
                    ])
                    ->required(),
                Select::make('severity')
                    ->label('شدت')
                    ->options([
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'بالا',
                    ])
                    ->required(),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('عنوان')->searchable(),
                TextColumn::make('rule_code')->label('کد کنترل'),
                BadgeColumn::make('severity')->label('شدت'),
                BadgeColumn::make('status')->label('وضعیت'),
                TextColumn::make('fund.name')->label('تنخواه'),
                TextColumn::make('detected_at')->label('تاریخ')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'open' => 'باز',
                        'triaged' => 'در حال بررسی',
                        'resolved' => 'حل‌شده',
                    ]),
                SelectFilter::make('severity')
                    ->options([
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'بالا',
                    ]),
            ])
            ->actions([
                Action::make('resolve')
                    ->label('حل شد')
                    ->visible(fn (PettyCashControlException $record) => $record->status !== 'resolved')
                    ->authorize(fn (PettyCashControlException $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(function (PettyCashControlException $record): void {
                        $record->update([
                            'status' => 'resolved',
                            'resolved_at' => now(),
                            'resolved_by' => auth()->id(),
                        ]);
                    }),
            ])
            ->defaultSort('detected_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPettyCashControlExceptions::route('/'),
            'edit' => EditPettyCashControlException::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return static::scopeByTenant(parent::getEloquentQuery()->with(static::$eagerLoad));
    }
}
