<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentWorkhub\Filament\Resources\LabelResource\Pages\CreateLabel;
use Haida\FilamentWorkhub\Filament\Resources\LabelResource\Pages\EditLabel;
use Haida\FilamentWorkhub\Filament\Resources\LabelResource\Pages\ListLabels;
use Haida\FilamentWorkhub\Models\Label;

class LabelResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.label';

    protected static ?string $model = Label::class;

    protected static ?string $navigationLabel = 'برچسب‌ها';

    protected static ?string $pluralModelLabel = 'برچسب‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

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
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                ColorPicker::make('color')
                    ->label('رنگ')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('slug')->label('اسلاگ'),
                TextColumn::make('color')->label('رنگ'),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLabels::route('/'),
            'create' => CreateLabel::route('/create'),
            'edit' => EditLabel::route('/{record}/edit'),
        ];
    }
}
