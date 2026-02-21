<?php

namespace Haida\FilamentThreeCx\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxCallLogResource\Pages\ListThreeCxCallLogs;
use Haida\FilamentThreeCx\Filament\Resources\ThreeCxCallLogResource\Pages\ViewThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Support\ThreeCxLabels;

class ThreeCxCallLogResource extends Resource
{
    protected static ?string $model = ThreeCxCallLog::class;

    protected static ?string $tenantOwnershipRelationshipName = 'tenant';

    protected static ?string $modelLabel = 'لاگ تماس';

    protected static ?string $pluralModelLabel = 'لاگ‌های تماس';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationLabel = 'تماس‌ها';

    protected static string|\UnitEnum|null $navigationGroup = '3CX';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return IamAuthorization::allows('threecx.view');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('started_at')->label('شروع')->jalaliDateTime()->sortable(),
                TextColumn::make('direction')
                    ->label('جهت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ThreeCxLabels::direction($state)),
                TextColumn::make('from_number')->label('از'),
                TextColumn::make('to_number')->label('به'),
                TextColumn::make('duration')->label('مدت (ثانیه)')->numeric(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn ($state) => ThreeCxLabels::status($state)),
                TextColumn::make('external_id')->label('شناسه خارجی')->toggleable(),
            ])
            ->filters([
                Filter::make('started_at')
                    ->label('بازه تاریخ')
                    ->form([
                        DatePicker::make('from')->label('از تاریخ'),
                        DatePicker::make('until')->label('تا تاریخ'),
                    ])
                    ->query(function ($query, array $data) {
                        if (! empty($data['from'])) {
                            $query->whereDate('started_at', '>=', $data['from']);
                        }
                        if (! empty($data['until'])) {
                            $query->whereDate('started_at', '<=', $data['until']);
                        }
                    }),
                SelectFilter::make('direction')
                    ->label('جهت')
                    ->options(fn () => ThreeCxCallLog::query()
                        ->whereNotNull('direction')
                        ->distinct()
                        ->pluck('direction')
                        ->mapWithKeys(fn ($value) => [$value => ThreeCxLabels::direction($value)])
                        ->toArray()),
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options(fn () => ThreeCxCallLog::query()
                        ->whereNotNull('status')
                        ->distinct()
                        ->pluck('status')
                        ->mapWithKeys(fn ($value) => [$value => ThreeCxLabels::status($value)])
                        ->toArray()),
                Filter::make('number')
                    ->label('شماره')
                    ->form([
                        TextInput::make('value')->label('شماره'),
                    ])
                    ->query(function ($query, array $data) {
                        $value = trim((string) ($data['value'] ?? ''));
                        if ($value !== '') {
                            $query->where(function ($sub) use ($value) {
                                $sub->where('from_number', 'like', '%'.$value.'%')
                                    ->orWhere('to_number', 'like', '%'.$value.'%');
                            });
                        }
                    }),
            ])
            ->actions([
                ViewAction::make()->label('جزئیات')->slideOver(),
            ])
            ->defaultSort('started_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListThreeCxCallLogs::route('/'),
            'view' => ViewThreeCxCallLog::route('/{record}'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('تماس')
                    ->schema([
                        TextEntry::make('direction')
                            ->label('جهت')
                            ->formatStateUsing(fn ($state) => ThreeCxLabels::direction($state)),
                        TextEntry::make('status')
                            ->label('وضعیت')
                            ->formatStateUsing(fn ($state) => ThreeCxLabels::status($state)),
                        TextEntry::make('from_number')->label('از'),
                        TextEntry::make('to_number')->label('به'),
                        TextEntry::make('started_at')->label('شروع')->jalaliDateTime(),
                        TextEntry::make('ended_at')->label('پایان')->jalaliDateTime(),
                        TextEntry::make('duration')->label('مدت (ثانیه)')->numeric(),
                        TextEntry::make('external_id')->label('شناسه خارجی'),
                        TextEntry::make('raw_payload')
                            ->label('جزئیات خام')
                            ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
