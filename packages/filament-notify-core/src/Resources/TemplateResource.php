<?php

namespace Haida\FilamentNotify\Core\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentNotify\Core\Channels\ChannelRegistry;
use Haida\FilamentNotify\Core\Models\Template;
use Haida\FilamentNotify\Core\Resources\TemplateResource\Pages\CreateTemplate;
use Haida\FilamentNotify\Core\Resources\TemplateResource\Pages\EditTemplate;
use Haida\FilamentNotify\Core\Resources\TemplateResource\Pages\ListTemplates;
use Illuminate\Database\Eloquent\Builder;

class TemplateResource extends Resource
{
    protected static ?string $model = Template::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'قالب‌ها';

    protected static string|\UnitEnum|null $navigationGroup = 'اطلاع‌رسانی';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        $channels = collect(app(ChannelRegistry::class)->installed())
            ->mapWithKeys(fn ($driver) => [$driver->key() => $driver->label()])
            ->toArray();

        return $schema
            ->components([
                TextInput::make('name')
                    ->label('نام قالب')
                    ->required()
                    ->maxLength(200),
                Select::make('channel')
                    ->label('کانال')
                    ->options($channels)
                    ->required(),
                TextInput::make('subject')
                    ->label('موضوع')
                    ->helperText('برای کانال‌هایی مثل ایمیل قابل استفاده است.')
                    ->maxLength(255),
                Textarea::make('body')
                    ->label('متن پیام')
                    ->required()
                    ->rows(8)
                    ->columnSpanFull(),
                Textarea::make('meta')
                    ->label('متادیتا (JSON)')
                    ->rows(6)
                    ->helperText('برای کانال‌های خاص (مثل واتساپ/IPPANEL) متادیتا را به‌صورت JSON وارد کنید.')
                    ->dehydrateStateUsing(static function ($state) {
                        if (is_array($state)) {
                            return $state;
                        }

                        $decoded = json_decode($state ?: '[]', true);
                        return is_array($decoded) ? $decoded : [];
                    })
                    ->formatStateUsing(static function ($state) {
                        if (is_string($state)) {
                            return $state;
                        }

                        return json_encode($state ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    })
                    ->rules(['json'])
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('channel')->label('کانال')->badge(),
                TextColumn::make('updated_at')->label('آخرین بروزرسانی')->dateTime(),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTemplates::route('/'),
            'create' => CreateTemplate::route('/create'),
            'edit' => EditTemplate::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->forPanel();
    }
}
