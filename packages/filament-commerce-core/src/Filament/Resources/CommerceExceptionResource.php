<?php

namespace Haida\FilamentCommerceCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceExceptionResource\Pages\CreateCommerceException;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceExceptionResource\Pages\EditCommerceException;
use Haida\FilamentCommerceCore\Filament\Resources\CommerceExceptionResource\Pages\ListCommerceExceptions;
use Haida\FilamentCommerceCore\Models\CommerceException;
use Illuminate\Database\Eloquent\Model;

class CommerceExceptionResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = CommerceException::class;

    protected static ?string $modelLabel = 'پرونده انطباق';

    protected static ?string $pluralModelLabel = 'پرونده‌های انطباق';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static string|\UnitEnum|null $navigationGroup = 'انطباق';

    public static function canViewAny(): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage']);
    }

    public static function canView(Model $record): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage'], IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canCreate(): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage');
    }

    public static function canEdit(Model $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function canDelete(Model $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record));
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('type')
                    ->label('نوع')
                    ->required()
                    ->maxLength(255),
                Select::make('severity')
                    ->label('شدت')
                    ->options([
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ])
                    ->default('medium')
                    ->required(),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'open' => 'باز',
                        'resolved' => 'حل شده',
                        'ignored' => 'نادیده گرفته',
                    ])
                    ->default('open')
                    ->required(),
                TextInput::make('title')
                    ->label('عنوان')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->nullable(),
                TextInput::make('entity_type')
                    ->label('نوع مرجع')
                    ->maxLength(255)
                    ->nullable(),
                TextInput::make('entity_id')
                    ->label('شناسه مرجع')
                    ->numeric()
                    ->nullable(),
                DateTimePicker::make('resolved_at')
                    ->label('زمان حل')
                    ->nullable(),
                Textarea::make('resolution_note')
                    ->label('یادداشت حل')
                    ->rows(2)
                    ->nullable(),
                Textarea::make('metadata')
                    ->label('متادیتا (JSON)')
                    ->rows(3)
                    ->nullable()
                    ->rules(['nullable', 'json'])
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }

                        return $state;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        if (! is_string($state) || trim($state) === '') {
                            return null;
                        }

                        $decoded = json_decode($state, true);

                        return is_array($decoded) ? $decoded : null;
                    }),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('نوع')
                    ->searchable(),
                TextColumn::make('severity')
                    ->label('شدت')
                    ->badge(),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge(),
                TextColumn::make('title')
                    ->label('عنوان')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('ایجاد')
                    ->jalaliDateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('وضعیت')
                    ->options([
                        'open' => 'باز',
                        'resolved' => 'حل شده',
                        'ignored' => 'نادیده گرفته',
                    ]),
                SelectFilter::make('severity')
                    ->label('شدت')
                    ->options([
                        'low' => 'کم',
                        'medium' => 'متوسط',
                        'high' => 'زیاد',
                    ]),
            ])
            ->actions([
                Action::make('resolve')
                    ->label('حل شد')
                    ->icon('heroicon-o-check')
                    ->visible(fn (CommerceException $record) => $record->status === 'open')
                    ->authorize(fn (CommerceException $record) => auth()->user()?->can('resolve', $record) ?? false)
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('resolution_note')
                            ->label('یادداشت حل')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->action(function (CommerceException $record, array $data): void {
                        $record->update([
                            'status' => 'resolved',
                            'resolved_at' => now(),
                            'resolved_by_user_id' => auth()->id(),
                            'resolution_note' => $data['resolution_note'] ?? null,
                        ]);
                    }),
                Action::make('ignore')
                    ->label('نادیده')
                    ->icon('heroicon-o-no-symbol')
                    ->visible(fn (CommerceException $record) => $record->status === 'open')
                    ->authorize(fn (CommerceException $record) => auth()->user()?->can('resolve', $record) ?? false)
                    ->requiresConfirmation()
                    ->form([
                        Textarea::make('resolution_note')
                            ->label('یادداشت')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->action(function (CommerceException $record, array $data): void {
                        $record->update([
                            'status' => 'ignored',
                            'resolved_at' => now(),
                            'resolved_by_user_id' => auth()->id(),
                            'resolution_note' => $data['resolution_note'] ?? null,
                        ]);
                    }),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCommerceExceptions::route('/'),
            'create' => CreateCommerceException::route('/create'),
            'edit' => EditCommerceException::route('/{record}/edit'),
        ];
    }
}
