<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentWorkhub\Filament\Resources\WorkTypeResource\Pages\CreateWorkType;
use Haida\FilamentWorkhub\Filament\Resources\WorkTypeResource\Pages\EditWorkType;
use Haida\FilamentWorkhub\Filament\Resources\WorkTypeResource\Pages\ListWorkTypes;
use Haida\FilamentWorkhub\Models\WorkType;

class WorkTypeResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.work_type';

    protected static ?string $model = WorkType::class;

    protected static ?string $navigationLabel = 'نوع‌های کار';

    protected static ?string $pluralModelLabel = 'نوع‌های کار';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

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
                TextInput::make('icon')
                    ->label('آیکن')
                    ->maxLength(255)
                    ->nullable(),
                ColorPicker::make('color')
                    ->label('رنگ')
                    ->nullable(),
                TextInput::make('sort_order')
                    ->label('ترتیب')
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label('فعال')
                    ->default(true),
                Textarea::make('description')
                    ->label('توضیحات')
                    ->rows(3)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                TextColumn::make('slug')->label('اسلاگ'),
                TextColumn::make('icon')->label('آیکن'),
                TextColumn::make('color')->label('رنگ'),
                TextColumn::make('sort_order')->label('ترتیب'),
                IconColumn::make('is_active')->label('فعال')->boolean(),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkTypes::route('/'),
            'create' => CreateWorkType::route('/create'),
            'edit' => EditWorkType::route('/{record}/edit'),
        ];
    }
}
