<?php

namespace Haida\FilamentWorkhub\Filament\Resources;

use Filamat\IamSuite\Filament\Concerns\InteractsWithTenant;
use Filamat\IamSuite\Filament\Resources\IamResource;
use Haida\FilamentWorkhub\Filament\Resources\WorkflowResource\Pages\CreateWorkflow;
use Haida\FilamentWorkhub\Filament\Resources\WorkflowResource\Pages\EditWorkflow;
use Haida\FilamentWorkhub\Filament\Resources\WorkflowResource\Pages\ListWorkflows;
use Haida\FilamentWorkhub\Filament\Resources\WorkflowResource\RelationManagers\StatusesRelationManager;
use Haida\FilamentWorkhub\Filament\Resources\WorkflowResource\RelationManagers\TransitionsRelationManager;
use Haida\FilamentWorkhub\Models\Workflow;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkflowResource extends IamResource
{
    use InteractsWithTenant;

    protected static ?string $permissionPrefix = 'workhub.workflow';

    protected static ?string $model = Workflow::class;

    protected static ?string $navigationLabel = 'گردش‌کارها';

    protected static ?string $pluralModelLabel = 'گردش‌کارها';

    protected static bool $hasTitleCaseModelLabel = false;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static string|\UnitEnum|null $navigationGroup = 'رهگیری کارها';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::tenantSelect(),
                TextInput::make('name')->label('نام')->required()->maxLength(255),
                Textarea::make('description')->label('توضیحات')->rows(3)->nullable(),
                Toggle::make('is_default')->label('پیش‌فرض')->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('نام')->searchable(),
                IconColumn::make('is_default')->label('پیش‌فرض')->boolean(),
                TextColumn::make('updated_at')->label('به‌روزرسانی'),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('is_default');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkflows::route('/'),
            'create' => CreateWorkflow::route('/create'),
            'edit' => EditWorkflow::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            StatusesRelationManager::class,
            TransitionsRelationManager::class,
        ];
    }
}
