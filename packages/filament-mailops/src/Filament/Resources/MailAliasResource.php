<?php

declare(strict_types=1);

namespace Haida\FilamentMailOps\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentMailOps\Filament\Resources\MailAliasResource\Pages\CreateMailAlias;
use Haida\FilamentMailOps\Filament\Resources\MailAliasResource\Pages\EditMailAlias;
use Haida\FilamentMailOps\Filament\Resources\MailAliasResource\Pages\ListMailAliases;
use Haida\FilamentMailOps\Models\MailAlias;
use Haida\FilamentMailOps\Models\MailDomain;
use Haida\FilamentMailOps\Services\MailuSyncService;
use Haida\FilamentMailOps\Support\MailOpsLabels;
use Illuminate\Database\Eloquent\Builder;

class MailAliasResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $model = MailAlias::class;

    protected static ?string $modelLabel = 'نام مستعار ایمیل';

    protected static ?string $pluralModelLabel = 'نام‌های مستعار ایمیل';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-at-symbol';

    protected static string|\UnitEnum|null $navigationGroup = 'ایمیل';

    protected static ?string $permissionPrefix = 'mailops.alias';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('domain_id')
                    ->label('دامنه')
                    ->options(fn () => static::scopeByTenant(MailDomain::query())
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('source')
                    ->label('آدرس مستعار')
                    ->email()
                    ->required(),
                TagsInput::make('destinations')
                    ->label('مقصدها')
                    ->required(),
                Toggle::make('is_wildcard')
                    ->label('Wildcard')
                    ->default(false),
                Select::make('status')
                    ->label('وضعیت')
                    ->options([
                        'active' => 'فعال',
                        'inactive' => 'غیرفعال',
                    ])
                    ->default('active')
                    ->required(),
                Textarea::make('comment')
                    ->label('یادداشت')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('domain'))
            ->columns([
                TextColumn::make('source')
                    ->label('آدرس')
                    ->searchable(),
                TextColumn::make('domain.name')
                    ->label('دامنه')
                    ->sortable(),
                TextColumn::make('destinations')
                    ->label('مقصدها')
                    ->formatStateUsing(fn (?array $state) => $state ? implode(', ', $state) : '-'),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => MailOpsLabels::status($state)),
                TextColumn::make('sync_status')
                    ->label('همگام‌سازی')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => MailOpsLabels::syncStatus($state)),
                TextColumn::make('mailu_synced_at')
                    ->label('آخرین همگام‌سازی')
                    ->jalaliDateTime(),
            ])
            ->actions([
                Action::make('sync_mailu')
                    ->label('همگام‌سازی Mailu')
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (MailAlias $record) => config('filament-mailops.mailu.enabled')
                        && IamAuthorization::allows('mailops.alias.manage', IamAuthorization::resolveTenantFromRecord($record)))
                    ->action(function (MailAlias $record, MailuSyncService $service): void {
                        $service->syncAlias($record);
                        Notification::make()->title('نام مستعار همگام شد.')->success()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailAliases::route('/'),
            'create' => CreateMailAlias::route('/create'),
            'edit' => EditMailAlias::route('/{record}/edit'),
        ];
    }
}
