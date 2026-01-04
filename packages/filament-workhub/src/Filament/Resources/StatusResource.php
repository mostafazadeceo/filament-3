<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Haida\FilamentWorkhub\Filament\Resources\StatusResource\Pages\CreateStatus;
use Haida\FilamentWorkhub\Filament\Resources\StatusResource\Pages\EditStatus;
use Haida\FilamentWorkhub\Filament\Resources\StatusResource\Pages\ListStatuses;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\Workflow;

class StatusResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.status';

    protected static ?string $model = Status::class;

    protected static ?string $navigationLabel = 'وضعیت‌ها';

    protected static ?string $pluralModelLabel = 'وضعیت‌ها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                Select::make('workflow_id')
                    ->label('گردش‌کار')
                    ->options(fn () => Workflow::query()->pluck('name', 'id')->toArray())
                    ->searchable()
                    ->required(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                TextInput::make('slug')->label('اسلاگ')->required()->maxLength(255),
                Select::make('category')
                    ->label('دسته')
                    ->options([
                        'todo' => 'کارهای جدید',
                        'in_progress' => 'در حال انجام',
                        'done' => 'انجام شده',
                    ])
                    ->required(),
                TextInput::make('color')->label('رنگ')->nullable(),
                TextInput::make('sort_order')->label('ترتیب')->numeric()->default(0),
                Toggle::make('is_default')->label('پیش‌فرض')->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workflow.name')->label('گردش‌کار'),
                TextColumn::make('name')->label('نام'),
                TextColumn::make('category')->label('دسته'),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean(),
                TextColumn::make('sort_order')->label('ترتیب'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStatuses::route('/'),
            'create' => CreateStatus::route('/create'),
            'edit' => EditStatus::route('/{record}/edit'),
        ];
    }
}
