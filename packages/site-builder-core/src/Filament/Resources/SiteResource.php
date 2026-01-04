<?php

namespace Haida\SiteBuilderCore\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\SiteBuilderCore\Enums\SiteStatus;
use Haida\SiteBuilderCore\Enums\SiteType;
use Haida\SiteBuilderCore\Filament\Resources\SiteResource\Pages\CreateSite;
use Haida\SiteBuilderCore\Filament\Resources\SiteResource\Pages\EditSite;
use Haida\SiteBuilderCore\Filament\Resources\SiteResource\Pages\ListSites;
use Haida\SiteBuilderCore\Models\Site;
use Haida\SiteBuilderCore\Services\SitePublisher;

class SiteResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'site_builder.site';

    protected static ?string $model = Site::class;

    protected static ?string $modelLabel = 'سایت';

    protected static ?string $pluralModelLabel = 'سایت ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static string|\UnitEnum|null $navigationGroup = 'سایت ساز';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')
                    ->label('نام')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->label('اسلاگ')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label('نوع')
                    ->options(SiteType::options())
                    ->default(SiteType::Website->value)
                    ->required(),
                TextInput::make('default_locale')
                    ->label('زبان')
                    ->default(fn () => config('site-builder-core.defaults.locale', 'fa_IR'))
                    ->maxLength(10),
                TextInput::make('currency')
                    ->label('واحد پول')
                    ->default(fn () => config('site-builder-core.defaults.currency', 'IRR'))
                    ->maxLength(10),
                TextInput::make('timezone')
                    ->label('منطقه زمانی')
                    ->default(fn () => config('site-builder-core.defaults.timezone', 'Asia/Tehran'))
                    ->maxLength(64),
                TextInput::make('theme_key')
                    ->label('کلید قالب')
                    ->default(fn () => config('site-builder-core.defaults.theme_key', 'relograde-v1'))
                    ->maxLength(64),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable()->sortable(),
                TextColumn::make('slug')->label('اسلاگ')->searchable(),
                TextColumn::make('type')
                    ->label('نوع')
                    ->formatStateUsing(fn (?string $state, Site $record) => $record->typeLabel()),
                TextColumn::make('status')
                    ->label('وضعیت')
                    ->badge()
                    ->formatStateUsing(fn (?string $state, Site $record) => $record->statusLabel()),
                TextColumn::make('published_at')->label('انتشار')->jalaliDate(),
            ])
            ->actions([
                Action::make('preview')
                    ->label('پیش نمایش')
                    ->visible(fn (Site $record) => $record->status !== SiteStatus::Preview->value)
                    ->authorize(fn (Site $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (Site $record) => app(SitePublisher::class)
                        ->preview($record, auth()->id())),
                Action::make('publish')
                    ->label('انتشار')
                    ->visible(fn (Site $record) => $record->status !== SiteStatus::Published->value)
                    ->authorize(fn (Site $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (Site $record) => app(SitePublisher::class)
                        ->publish($record, auth()->id())),
                Action::make('disable')
                    ->label('غیرفعال سازی')
                    ->visible(fn (Site $record) => $record->status !== SiteStatus::Disabled->value)
                    ->authorize(fn (Site $record) => auth()->user()?->can('update', $record) ?? false)
                    ->requiresConfirmation()
                    ->action(fn (Site $record) => app(SitePublisher::class)
                        ->disable($record, auth()->id())),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSites::route('/'),
            'create' => CreateSite::route('/create'),
            'edit' => EditSite::route('/{record}/edit'),
        ];
    }
}
